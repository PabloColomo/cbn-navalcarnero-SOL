"""Audit and prepare the approved CBN photography for the public theme.

The source JPEG files in ``images/`` are never modified. The script groups
byte-identical files, excludes three visually redundant re-exports selected by
the art-direction audit, strips metadata and writes responsive JPEG, WebP and
AVIF derivatives plus a runtime manifest for the WordPress theme.

Requires Pillow with WebP and AVIF support.
"""

from __future__ import annotations

import hashlib
import json
import shutil
import subprocess
import uuid
from collections import OrderedDict
from datetime import date
from pathlib import Path

try:
    from PIL import Image, ImageEnhance, ImageOps, ImageStat, features
except ImportError as exc:  # pragma: no cover - local tooling guard
    raise SystemExit("Pillow is required to prepare the club photographs.") from exc


REPOSITORY = Path(__file__).resolve().parents[1]
SOURCE_DIRECTORY = REPOSITORY / "images"
OUTPUT_DIRECTORY = (
    REPOSITORY
    / "wp-content"
    / "themes"
    / "cbn-theme"
    / "assets"
    / "src"
    / "images"
    / "club"
)
MANIFEST_PATH = OUTPUT_DIRECTORY / "manifest.json"
WIDTHS = (480, 960)

# IDs are tied to content, never to filenames. This prevents renamed or newly
# sorted WhatsApp exports from silently receiving the wrong alt text or usage.
EXPECTED_HASH_TO_ID = {
    "b847293354a5f681ec854dfd63187d44a063f3e653934629a430e44bb9666287": "club-001",
    "ee7ed86e75357d4b93b97ff4b0a32ba2a04fb885f1f38a8011ce42658f3e55d1": "club-002",
    "a6044984217ab80a82de0db39d2f2fb76ceda5af04dbf4a1ce7cf725d8bc99f4": "club-003",
    "d9dc09aa2b553b02d9e771cb0b3d2008666caef5cf3234a34458f89675a605c1": "club-004",
    "662f20e0784887b22a7bd8bf4b2fd16e5ef03bd9304d3a8621cca3b27494d69a": "club-005",
    "b857f7f1d3c0a4ef433268c4e8c27b2e01b2507f2ad0c02d73f3be9088087944": "club-006",
    "a8448f89fbefce8ff714607c588247d183c0759fe432ca7a079ac22826e60a48": "club-007",
    "fa7037f57302b03570a306dba4a72009079e2c37480245d3f1ff7329c9d84a24": "club-008",
    "aaf78ff87d8bd5df8cd6e991ee5fb54a86a362f67086d2cb4f2462dfa7f00a3f": "club-009",
    "01009e5fad64cc6bc65c4f16b47df449a529c173a78f0b95c8b571890dd33eb4": "club-010",
    "2da760a14610db234b6f4582553cb8862ee9aff5a8ddfed4ad02be6018f96b9c": "club-011",
    "afc5df6ba20a31e1a33156b47355527312044c8192f83c0f4bf77d422e0ee7d0": "club-012",
    "bdb67df9caff6a3572ba18fea4f4ee271ba76c0ae00f942ffeb6af607ec8849f": "club-013",
    "5d002a652e310a0b2e66f0716f569508405c8c1f3f1a9896365b02b6d90c6997": "club-014",
    "76396b76a0cab35b5547518ba03929ae2609f63e346813573e083c52d8a219d1": "club-015",
    "f428dddde2ce06d15a9a8da6793a4bc2b5c62b9c7928565c36f4d22364abe1dc": "club-016",
    "f6f6be743c71615063f88cab9cec3ccb2787d17b8b839e9c79e0805eaa241f91": "club-017",
    "aecb8db97fc9dc87590cd872f945c73a442dd3799cabad9cd438b7b44a10ceea": "club-018",
    "6cc0c34b9da8c1a93af4d34df3adb5b9c499c81f0ba3ffb538aa056e7385b3fd": "club-019",
    "b87437989ebe22c30c7471df0825c961d58921834676cc99d489e8f69d7ee517": "club-020",
    "499b47d50db9ef3dbb23b24005be579f752b1494886fe328d223c9803ea8fabb": "club-021",
    "8d1b4ae0c4736a0579c3fbc0ce5723cd52e60b0fb8158b6f29448e28e68e8d8c": "club-022",
    "d036030805d2db9666d07dc7ba6a9f87dca84ec98fc7976a802f33e57b0951e0": "club-023",
    "1075d2bd11778748cd762227a1ee06188fe1b673839756e3e030f7c1aef5ebb9": "club-024",
    "9b182192900c173a1cbd6c5a5904b5f764cabd5c4d07b87269dcac0906648761": "club-025",
    "62988a2db40da2831692068358d8b3e4044bcfe160e2d301b4ee726bc68c9e0b": "club-026",
    "c200a47323243f68559a4a6854e7c8b2cc27546cd7f98ae38cd3c52b4fafe2a9": "club-027",
    "8abc9932fceaf23c821d470d8de3c2137d174d5f311c04d9e39060af94b4f657": "club-028",
    "2f3a21e80c2561e25dece026e62a32d868701c08961410b4e0069cc78f42f42c": "club-029",
    "6844a9d5d1fa7bee1d1ea06cfae5c716b248a03300c87d2646eef6ae688cf87e": "club-030",
    "fe8929970dc06b133f7cae0057fe86fecc47dacfef7563174252e9ccb9754e41": "club-031",
    "8c5b35f6bedf00b10c023480c698b00afc4401bbb11a41c950f052cd296d6232": "club-032",
    "792040cb9d83a1dc117dbad23aca7fc46f76a7a98b5cdbf7f59d176b22fb3a13": "club-033",
    "69ad0ef99690e2ed5b072f425cc713fd56bf2f92bdd9263db1c612542a0db12c": "club-034",
    "52bb73fb1cad9dbe1d46856f221dac4d0ab8933e9ef7f520f7acc4ea8bd79031": "club-035",
    "3da25a7815dd038b9b583f6b436852ee431cf54fe7160cde17bd437fe7341016": "club-036",
    "37f60cd2d56f594d9017be723eefd78e2ba421b6cec9146734d1417c0e411f76": "club-037",
    "395ad3113cf28212ceca94700689a9cd427f994aba9549fb821fa1047b91c7b9": "club-038",
    "f6930842ebc34ed7af6e6379132dd8d695bf0235fa040094a9e32f65239ffd89": "club-039",
    "30d5821d72d87a337979e67a4409b50d62c39aeddd81aa97402dd4b0e70a2a62": "club-040",
    "4afb105291505f581edb9b32df72769f3080c14bd9e7f4a3ca3be17872111364": "club-041",
    "c1290ff9f22f21df28528312f0a2a6ef0f80a0d91826e3922b499348deb88749": "club-042",
    "2d607aface93a44547a8f0ba98012098cc9065ac1866ca4143d52086f6b0e554": "club-043",
    "dd0665fcee86e2b0468f86d685d54f743a7b0eecfefb5d60f922963e86e82804": "club-044",
    "40a9cb8be9719bdd51f98ee3bc4884703a226b794cafc160033f1084fab61ff2": "club-045",
    "4be94d4a33dfc43c6215e0c56dc801f370c4635ab9e90d8da7560888149b0405": "club-046",
    "96474e65ff1aa6e63db1661b331c270a64e96d75389767bb279a16196e3150aa": "club-047",
    "5fc13a2e93105e803ae1bdf18488b39ba0ef9ee54a9eb17b5db26f42661c0b49": "club-048",
    "7dc2390d08531a45e159fc7e36d99aaeed34dfecd99b8fb65b07d250f3949beb": "club-049",
    "46ae9b769d6f880e894c8e87c93ed705c6eff46ad0dde620ad7b9a32e529e24e": "club-050",
    "d2c3883558bb270a2ca75e10f357d743793d944b523b57f75efd57102201d66a": "club-051",
    "e87aaa78d62f9d2a1672bf9224dc0e0aeff691544f4aac3a0e089a8dab1b0480": "club-052",
    "529663fdf7d9c9f34e99a78b459e56df7db73ffb426a61531379ab2c77d6a4cb": "club-053",
    "2fd552705c75c6bd466fbee99d9a676568e4e7fd81c7d82745e05622557b0639": "club-054",
    "e9c177adaff8f4edbb8e3e4d5fd7fa7a17237508a0953ec90891f2bd33cdf8a8": "club-055",
    "e8886fc8377ccf16903d2ec1dfc6076643d99a5566defaa2af1e93a716f13c89": "club-056",
    "737d8347048d203d08a259c3b69dbe2bd53a4dd8eef481d751c81042a5fb5d6f": "club-057",
    "754df7eba6ab4da3a31dcb61c116559f8e0cd0803f879de05cd0eb05cee7e877": "club-058",
    "46bbae928b7b04a683c4efcee63b645afa247569c9411742fda1fc2c713d5871": "club-059",
    "88a04ce1d9aac44e81415397497ba2b1552f4e6dba4e4c9c59f6fa8bf757bdd2": "club-060",
    "6996942246a31d0962dd5499b6cdba78fa78422779014920e4b155a0db913214": "club-061",
    "04de7fdf1e63c85d636258c925b8d9501417c5c68f2b72f35d36dad20e28cf20": "club-062",
    "33f87b0aa82e2be55393af132ed6f841434b563facb4ded987b6a4080be734b2": "club-063",
    "37cce1e352ad43978c52f25c54bcfc372b2aafc78aa5a95227aab47bf283a219": "club-064",
    "b19f5f1a662ee88149396f31692475ea26da87da9da2d09c42ebd65be6d1a0bd": "club-065",
    "5ea722498fada72f135829c8d19c85dc9402119d5c1e320f240f44d30a3a1a93": "club-066",
}

# These are the better-exposed representatives of three visually redundant
# re-exports. IDs are stable because exact representatives are sorted by name.
NEAR_DUPLICATES = {
    "club-003": {
        "duplicate_of": "club-001",
        "reason": "Mismo detalle de pierna, tatuaje y zapatilla con un recorte más cerrado y sin valor narrativo nuevo.",
    },
    "club-060": {
        "duplicate_of": "club-032",
        "reason": "Misma toma reexportada con exposición notablemente más oscura.",
    },
    "club-062": {
        "duplicate_of": "club-064",
        "reason": "Mismo encuadre de manos unidas con menos luz y sin valor narrativo nuevo.",
    },
}

PHOTO_NOTES = {
    "club-001": ("Detalle de zapatilla y pierna de un jugador sobre la pista.", "detalle"),
    "club-002": ("Jugador del CBN lanzando a canasta ante la defensa rival.", "acción"),
    "club-004": ("Zapatillas negras de un jugador detenido sobre la pista.", "detalle"),
    "club-005": ("Jugador del CBN celebrando una acción del partido.", "emoción"),
    "club-006": ("Jugador del CBN concentrado con el balón antes de un lanzamiento.", "concentración"),
    "club-007": ("Jugador del CBN finalizando cerca del aro ante varios defensores.", "acción"),
    "club-008": ("Jugadores del CBN preparando un lanzamiento desde la pista.", "equipo"),
    "club-009": ("Jugador del CBN elevando el balón para lanzar.", "acción"),
    "club-010": ("Jugador del CBN recuperando el aliento durante el partido.", "esfuerzo"),
    "club-011": ("Jugador del CBN avanzando con un bote bajo frente a un defensor.", "acción"),
    "club-012": ("Lanzamiento del CBN con la defensa rival delante.", "acción"),
    "club-013": ("Detalle de unas zapatillas negras apoyadas en la línea de pista.", "detalle"),
    "club-014": ("Jugador del CBN expresando intensidad tras una jugada.", "emoción"),
    "club-015": ("Jugador del CBN desplazándose con el balón.", "acción"),
    "club-016": ("Jugador del CBN reaccionando con energía durante el partido.", "emoción"),
    "club-017": ("Jugador del CBN lanzando a canasta con el aro al fondo.", "acción"),
    "club-018": ("Jugador del CBN celebrando el esfuerzo de una posesión.", "emoción"),
    "club-019": ("Jugador del CBN armando un lanzamiento frontal.", "acción"),
    "club-020": ("Jugador del CBN ejecutando un tiro libre.", "concentración"),
    "club-021": ("Jugador del CBN penetrando con el balón ante la defensa.", "acción"),
    "club-022": ("Detalle de zapatillas blancas sobre las líneas de la cancha.", "detalle"),
    "club-023": ("Jugador del CBN entrando a canasta con el balón elevado.", "acción"),
    "club-024": ("Detalle de zapatillas blancas durante una pausa del partido.", "detalle"),
    "club-025": ("Detalle de la camiseta del CBN y el balón en las manos de un jugador.", "identidad"),
    "club-026": ("Detalle de zapatillas de baloncesto apoyadas sobre el parqué.", "detalle"),
    "club-027": ("Jugador del CBN celebrando con intensidad una jugada.", "emoción"),
    "club-028": ("Jugador con equipación blanca del CBN lanzando un tiro libre.", "concentración"),
    "club-029": ("Jugador del CBN protegiendo el bote ante un rival.", "acción"),
    "club-030": ("Jugador del CBN lanzando ante dos defensores.", "acción"),
    "club-031": ("Jugadores y cuerpo técnico del CBN reunidos junto al banquillo.", "equipo"),
    "club-032": ("Jugador con equipación blanca del CBN elevándose para lanzar.", "acción"),
    "club-033": ("Jugador del CBN esperando con el balón junto a la banda.", "concentración"),
    "club-034": ("Jugador del CBN preparándose para un tiro libre.", "concentración"),
    "club-035": ("Lanzamiento del CBN en una pista con banderas y grada al fondo.", "competición"),
    "club-036": ("Jugador del CBN lanzando por encima de un defensor.", "acción"),
    "club-037": ("Detalle del brazo y la equipación de un jugador en la pista.", "identidad"),
    "club-038": ("Jugador del CBN de pie con el balón antes de reanudar el juego.", "concentración"),
    "club-039": ("Jugador del CBN finalizando cerca del aro en competición.", "competición"),
    "club-040": ("Jugador del CBN elevándose con el balón hacia canasta.", "acción"),
    "club-041": ("Jugador del CBN protegiendo el balón de espaldas a la defensa.", "acción"),
    "club-042": ("Jugador del CBN concentrado en la línea de tiro libre.", "concentración"),
    "club-043": ("Lanzamiento del CBN disputado por dos defensores.", "acción"),
    "club-044": ("Detalle de zapatillas de colores sobre las líneas de la pista.", "detalle"),
    "club-045": ("Jugador del CBN caminando con el balón durante el partido.", "concentración"),
    "club-046": ("Jugador del CBN atacando el aro con la mano extendida.", "acción"),
    "club-047": ("Primer plano de manos, balón y zapatillas antes de un lanzamiento.", "detalle"),
    "club-048": ("Jugador del CBN ejecutando un tiro libre en la pista.", "concentración"),
    "club-049": ("Jugador del CBN avanzando entre varios defensores.", "acción"),
    "club-050": ("Jugador del CBN preparado con el balón en la línea de tiro libre.", "concentración"),
    "club-051": ("Entrada a canasta del CBN defendida por un rival.", "acción"),
    "club-052": ("Entrenador y jugador del CBN conversando junto al banquillo.", "acompañamiento"),
    "club-053": ("Jugador del CBN lanzando a canasta ante un defensor.", "acción"),
    "club-054": ("Jugador del CBN luchando por el balón cerca del aro.", "acción"),
    "club-055": ("Jugador del CBN esperando con el balón y la canasta al fondo.", "concentración"),
    "club-056": ("Jugador del CBN entrando a canasta ante la oposición rival.", "acción"),
    "club-057": ("Balón de baloncesto apoyado sobre las líneas de la pista.", "detalle"),
    "club-058": ("Jugador del CBN atacando el aro entre varios rivales.", "acción"),
    "club-059": ("Jugador del CBN acelerando el bote frente a un defensor.", "acción"),
    "club-061": ("Balones de baloncesto preparados junto a la pista.", "detalle"),
    "club-063": ("Jugador del CBN avanzando con el balón en velocidad.", "acción"),
    "club-064": ("Integrantes del CBN uniendo las manos en un gesto de equipo.", "comunidad"),
    "club-065": ("Jugador del CBN concentrado antes de lanzar un tiro libre.", "concentración"),
    "club-066": ("Jugador del CBN preparando un lanzamiento junto al banquillo.", "concentración"),
}

HIGH_FOCAL_IDS = {
    "club-002",
    "club-007",
    "club-009",
    "club-012",
    "club-017",
    "club-019",
    "club-020",
    "club-023",
    "club-028",
    "club-030",
    "club-032",
    "club-034",
    "club-035",
    "club-036",
    "club-039",
    "club-040",
    "club-042",
    "club-043",
    "club-046",
    "club-048",
    "club-049",
    "club-050",
    "club-051",
    "club-053",
    "club-054",
    "club-056",
    "club-058",
    "club-065",
    "club-066",
}

STORIES = {
    "home": {
        "eyebrow": "El club en imágenes",
        "title": "La pista se vive desde dentro",
        "photos": [
            "club-007",
            "club-011",
            "club-014",
            "club-021",
            "club-028",
            "club-029",
            "club-035",
            "club-046",
            "club-047",
            "club-058",
        ],
    },
    "club": {
        "eyebrow": "Comunidad CBN",
        "title": "Esfuerzo, identidad y equipo",
        "photos": [
            "club-001",
            "club-005",
            "club-010",
            "club-013",
            "club-024",
            "club-008",
            "club-037",
            "club-016",
            "club-018",
            "club-004",
            "club-025",
            "club-027",
        ],
    },
    "teams": {
        "eyebrow": "Equipos CBN",
        "title": "Concentración, compromiso y juego",
        "photos": [
            "club-006",
            "club-008",
            "club-009",
            "club-014",
            "club-015",
            "club-016",
            "club-017",
            "club-018",
            "club-019",
            "club-020",
        ],
    },
    "matches": {
        "eyebrow": "Competición",
        "title": "Cada posesión cuenta",
        "photos": [
            "club-002",
            "club-011",
            "club-012",
            "club-023",
            "club-030",
            "club-032",
            "club-036",
            "club-041",
            "club-043",
            "club-049",
            "club-051",
            "club-054",
            "club-056",
        ],
    },
    "news": {
        "eyebrow": "Actualidad CBN",
        "title": "Momentos que cuentan la temporada",
        "photos": [
            "club-028",
            "club-033",
            "club-034",
            "club-038",
            "club-042",
            "club-045",
            "club-050",
            "club-055",
        ],
    },
    "registration": {
        "eyebrow": "Tu primera jugada",
        "title": "Aprender, disfrutar y formar parte",
        "photos": [
            "club-047",
            "club-048",
            "club-059",
            "club-063",
            "club-065",
            "club-066",
            "club-057",
            "club-061",
        ],
    },
    "shop": {
        "eyebrow": "Identidad CBN",
        "title": "Los detalles que nos acompañan",
        "photos": ["club-004", "club-022", "club-026", "club-044"],
    },
    "contact": {
        "eyebrow": "Cerca de la pista",
        "title": "Un club que escucha y acompaña",
        "photos": ["club-031", "club-052", "club-064"],
    },
    "sponsors": {
        "eyebrow": "Comunidad local",
        "title": "Impulsamos el baloncesto juntos",
        "photos": ["club-035", "club-039", "club-040", "club-053"],
    },
}

FEATURED_PLACEMENTS = {
    "home_hero": ["club-051", "club-030", "club-027"],
    "home_team_fallbacks": ["club-057", "club-061", "club-022", "club-026"],
    "home_manifesto": ["club-031"],
    "home_news_fallbacks": ["club-002", "club-052", "club-041", "club-061"],
    "home_registration": ["club-064"],
    "home_shop": ["club-025"],
    "club_hero": ["club-031", "club-052", "club-064"],
    "club_facilities": ["club-057", "club-026"],
    "club_school": ["club-061"],
    "shop_hero": ["club-025"],
    "news_fallbacks": [
        "club-002",
        "club-007",
        "club-021",
        "club-027",
        "club-029",
        "club-041",
        "club-046",
        "club-058",
        "club-012",
        "club-023",
    ],
}


def sha256(path: Path) -> str:
    digest = hashlib.sha256()
    with path.open("rb") as stream:
        for chunk in iter(lambda: stream.read(1024 * 1024), b""):
            digest.update(chunk)
    return digest.hexdigest()


def exact_groups() -> OrderedDict[str, list[Path]]:
    groups: OrderedDict[str, list[Path]] = OrderedDict()
    for path in sorted(SOURCE_DIRECTORY.glob("*.jpeg"), key=lambda item: item.name.lower()):
        groups.setdefault(sha256(path), []).append(path)
    return groups


def prepared_image(path: Path) -> Image.Image:
    with Image.open(path) as source:
        image = ImageOps.exif_transpose(source).convert("RGB")

    # A restrained correction keeps skin and club colours natural while
    # gently lifting the darker WhatsApp exports.
    luminance = ImageStat.Stat(image.convert("L").resize((1, 1))).mean[0]
    brightness = max(1.0, min(1.10, 110 / max(luminance, 1)))
    image = ImageEnhance.Brightness(image).enhance(brightness)
    image = ImageEnhance.Contrast(image).enhance(1.035)
    image = ImageEnhance.Color(image).enhance(1.025)
    return ImageEnhance.Sharpness(image).enhance(1.08)


def save_derivatives(
    identifier: str,
    image: Image.Image,
    output_directory: Path,
) -> dict[str, list[dict[str, object]]]:
    derivatives: dict[str, list[dict[str, object]]] = {
        "jpeg": [],
        "webp": [],
        "avif": [],
    }
    for width in WIDTHS:
        height = round(image.height * width / image.width)
        resized = image.resize((width, height), Image.Resampling.LANCZOS)
        variants = {
            "jpeg": ("jpg", {"quality": 82, "optimize": True, "progressive": True}),
            "webp": ("webp", {"quality": 78, "method": 6}),
            "avif": ("avif", {"quality": 50, "speed": 8}),
        }
        for format_name, (extension, options) in variants.items():
            filename = f"{identifier}-{width}.{extension}"
            target = output_directory / filename
            resized.save(target, **options)
            derivatives[format_name].append(
                {
                    "path": filename,
                    "width": width,
                    "height": height,
                    "bytes": target.stat().st_size,
                }
            )
    return derivatives


def main() -> None:
    if not SOURCE_DIRECTORY.is_dir():
        raise SystemExit(f"Source directory not found: {SOURCE_DIRECTORY}")
    if not features.check("webp") or not features.check("avif"):
        raise SystemExit("This Pillow build must support both WebP and AVIF.")

    groups = exact_groups()
    if sum(len(group) for group in groups.values()) != 94 or len(groups) != 66:
        raise SystemExit("Unexpected source inventory; expected 94 JPEGs and 66 exact representatives.")

    actual_hashes = set(groups)
    expected_hashes = set(EXPECTED_HASH_TO_ID)
    if actual_hashes != expected_hashes:
        missing_count = len(expected_hashes - actual_hashes)
        unexpected_count = len(actual_hashes - expected_hashes)
        raise SystemExit(
            "Unexpected source content; "
            f"{missing_count} approved hashes are missing and {unexpected_count} new hashes were found."
        )

    OUTPUT_DIRECTORY.parent.mkdir(parents=True, exist_ok=True)
    staging_directory = OUTPUT_DIRECTORY.parent / f".club-photos-{uuid.uuid4().hex}"
    staging_directory.mkdir()
    backup_directory = OUTPUT_DIRECTORY.parent / ".club-photos-backup"

    records: dict[str, dict[str, object]] = {}
    exact_discarded: list[dict[str, object]] = []
    near_discarded: list[dict[str, object]] = []

    try:
        for digest, identifier in sorted(EXPECTED_HASH_TO_ID.items(), key=lambda item: item[1]):
            files = groups[digest]
            representative = files[0]

            for duplicate in files[1:]:
                exact_discarded.append(
                    {
                        "source": duplicate.name,
                        "duplicate_of": identifier,
                        "sha256": digest,
                        "reason": "Duplicado exacto por SHA-256.",
                    }
                )

            if identifier in NEAR_DUPLICATES:
                near_discarded.append(
                    {
                        "id": identifier,
                        "source": representative.name,
                        "sha256": digest,
                        **NEAR_DUPLICATES[identifier],
                    }
                )
                continue

            if identifier not in PHOTO_NOTES:
                raise SystemExit(f"Missing visual notes for {identifier}")

            alt, content = PHOTO_NOTES[identifier]
            image = prepared_image(representative)
            records[identifier] = {
                "id": identifier,
                "alt": alt,
                "content": content,
                "focal_position": "50% 38%" if identifier in HIGH_FOCAL_IDS else "50% 50%",
                "derivatives": save_derivatives(identifier, image, staging_directory),
            }

        expected_ids = set(PHOTO_NOTES)
        if set(records) != expected_ids:
            missing = sorted(expected_ids.symmetric_difference(records))
            raise SystemExit(f"Unexpected publishable photo IDs: {', '.join(missing)}")

        placements: dict[str, list[str]] = {identifier: [] for identifier in records}
        story_ids: set[str] = set()
        for story_name, story in STORIES.items():
            for identifier in story["photos"]:
                if identifier not in records:
                    raise SystemExit(f"Unknown photo {identifier} in story {story_name}")
                placements[identifier].append(f"story:{story_name}")
                story_ids.add(identifier)
        for placement_name, identifiers in FEATURED_PLACEMENTS.items():
            for identifier in identifiers:
                if identifier not in records:
                    raise SystemExit(f"Unknown photo {identifier} in placement {placement_name}")
                placements[identifier].append(f"featured:{placement_name}")

        uncovered = sorted(set(records) - story_ids)
        if uncovered:
            raise SystemExit(
                "Photos without an unconditional editorial story: " + ", ".join(uncovered)
            )
        for identifier, uses in placements.items():
            records[identifier]["placements"] = uses

        manifest = {
            "version": 1,
            "generated_on": date.today().isoformat(),
            "source_files": 94,
            "exact_unique_files": 66,
            "visual_unique_publishable": len(records),
            "discarded_files": len(exact_discarded) + len(near_discarded),
            "discarded_summary": {
                "exact_duplicate_copies": len(exact_discarded),
                "near_duplicate_reexports": len(near_discarded),
                "near_duplicate_ids": sorted(item["id"] for item in near_discarded),
            },
            "processing": {
                "widths": list(WIDTHS),
                "formats": ["jpeg", "webp", "avif"],
                "exif_removed": True,
                "orientation_corrected": True,
                "crop": "none",
                "treatment": "Ajuste tonal suave, contraste +3,5 %, color +2,5 % y enfoque +8 %.",
            },
            "photos": records,
            "stories": STORIES,
            "featured_placements": FEATURED_PLACEMENTS,
        }
        staging_manifest_path = staging_directory / MANIFEST_PATH.name
        staging_manifest_path.write_text(
            json.dumps(manifest, ensure_ascii=False, indent=2) + "\n",
            encoding="utf-8",
        )

        prettier_cli = REPOSITORY / "node_modules" / "prettier" / "bin" / "prettier.cjs"
        node_executable = shutil.which("node")
        if prettier_cli.is_file() and node_executable:
            subprocess.run(
                [node_executable, str(prettier_cli), "--write", str(staging_manifest_path)],
                cwd=REPOSITORY,
                check=True,
            )
        else:
            print(
                "Prettier is not available; format manifest.json after installing project dependencies."
            )

        if backup_directory.exists():
            shutil.rmtree(backup_directory)

        published = False
        try:
            if OUTPUT_DIRECTORY.exists():
                OUTPUT_DIRECTORY.replace(backup_directory)
            staging_directory.replace(OUTPUT_DIRECTORY)
            published = True
        except Exception:
            if backup_directory.exists() and not OUTPUT_DIRECTORY.exists():
                backup_directory.replace(OUTPUT_DIRECTORY)
            raise
        finally:
            if published and backup_directory.exists():
                shutil.rmtree(backup_directory)
    finally:
        if staging_directory.exists():
            shutil.rmtree(staging_directory)

    print(f"Prepared {len(records)} publishable photographs in {OUTPUT_DIRECTORY}")
    print(f"Discarded {len(exact_discarded)} exact copies and {len(near_discarded)} near duplicates")
    print(f"Manifest: {MANIFEST_PATH}")


if __name__ == "__main__":
    main()
