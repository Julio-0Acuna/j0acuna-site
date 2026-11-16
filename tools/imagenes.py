"""Imágenes del portafolio.

Uso:
  python -I tools/imagenes.py webp ENTRADA SALIDA      (ancho máx. 1200, calidad 80)
  python -I tools/imagenes.py og TITULO SUBTITULO SALIDA  (1200x630 JPG)
"""
import os
import sys

from PIL import Image, ImageDraw, ImageFilter, ImageFont

FUENTES = ["C:/Windows/Fonts/segoeuib.ttf", "C:/Windows/Fonts/arialbd.ttf"]
FUENTES_REG = ["C:/Windows/Fonts/segoeui.ttf", "C:/Windows/Fonts/arial.ttf"]


def fuente(rutas, tam):
    for r in rutas:
        if os.path.exists(r):
            return ImageFont.truetype(r, tam)
    return ImageFont.load_default()


def a_webp(entrada, salida, ancho_max=1200, calidad=80):
    img = Image.open(entrada).convert("RGB")
    if img.width > ancho_max:
        img = img.resize((ancho_max, round(img.height * ancho_max / img.width)), Image.LANCZOS)
    img.save(salida, "WEBP", quality=calidad, method=6)
    # Si todavía pesa más de 200 KB, bajar calidad.
    while os.path.getsize(salida) > 200 * 1024 and calidad > 40:
        calidad -= 10
        img.save(salida, "WEBP", quality=calidad, method=6)
    print(f"{salida}: {img.width}x{img.height}, {os.path.getsize(salida) // 1024} KB, q{calidad}")


def envolver(draw, texto, f, ancho):
    lineas, actual = [], ""
    for palabra in texto.split():
        prueba = (actual + " " + palabra).strip()
        if draw.textlength(prueba, font=f) <= ancho:
            actual = prueba
        else:
            lineas.append(actual)
            actual = palabra
    if actual:
        lineas.append(actual)
    return lineas


def og(titulo, subtitulo, salida):
    w, h = 1200, 630
    base = Image.new("RGB", (w, h), (7, 11, 18))
    brillo = Image.new("RGB", (w, h), (0, 0, 0))
    d = ImageDraw.Draw(brillo)
    d.ellipse((-200, -250, 600, 450), fill=(20, 120, 110))
    d.ellipse((800, -150, 1400, 400), fill=(120, 80, 10))
    brillo = brillo.filter(ImageFilter.GaussianBlur(160))
    img = Image.blend(base, brillo, 0.55)
    draw = ImageDraw.Draw(img)

    # Grilla sutil, como el fondo del sitio.
    for x in range(0, w, 44):
        draw.line([(x, 0), (x, h)], fill=(16, 32, 38), width=1)
    for y in range(0, h, 44):
        draw.line([(0, y), (w, y)], fill=(16, 32, 38), width=1)

    f_titulo = fuente(FUENTES, 84)
    f_sub = fuente(FUENTES_REG, 40)
    f_marca = fuente(FUENTES, 30)

    y = 150
    for linea in envolver(draw, titulo, f_titulo, w - 160):
        draw.text((80, y), linea, font=f_titulo, fill=(255, 255, 255))
        y += 96
    y += 20
    for linea in envolver(draw, subtitulo, f_sub, w - 160):
        draw.text((80, y), linea, font=f_sub, fill=(203, 213, 225))
        y += 52
    draw.rectangle((80, 520, 200, 526), fill=(94, 234, 212))
    draw.text((80, 545), "j0acuna.site · WhatsApp +56 9 7353 7642", font=f_marca, fill=(94, 234, 212))

    img.save(salida, "JPEG", quality=86, optimize=True, progressive=True)
    print(f"{salida}: {w}x{h}, {os.path.getsize(salida) // 1024} KB")


if __name__ == "__main__":
    if len(sys.argv) == 4 and sys.argv[1] == "webp":
        a_webp(sys.argv[2], sys.argv[3])
    elif len(sys.argv) == 5 and sys.argv[1] == "og":
        og(sys.argv[2], sys.argv[3], sys.argv[4])
    else:
        print(__doc__)
        sys.exit(2)
