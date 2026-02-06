# Resizly

This application provides a lightweight solution for on-the-fly image manipulation, specifically designed to replicate the syntax and functionality of Cloudflare Images URL transformations.

It allows you to serve optimized, resized, and processed images simply by setting parameters to your image URLs, reducing the need for manual pre-processing and saving storage space.

> [!WARNING]
> Not ready to use in production (doh)

### it supports following image formats as input:
- png
- jpg
- webp

### And can produce output (`format` option) in:
- png
- jpg
- webp (default and `auto`)
- avif

### Supported image resize backends
- GD
- Imagick
- Vips

### Parameters
| Parameter               | Supported   |
|-------------------------|-------------|
| anim                    | ❌           |
| border                  | ❌           |
| brightness              | ✅           |
| compression             | ❌           |
| contrast                | ❌           |
| dpr                     | ✅           |
| fit                     | ✅           |
| flip                    | ✅           |
| format                  | ✅ (no json) |
| gamma                   | ❌           |
| gravity                 | ❌           |
| height                  | ✅           |
| metadata                | ❌           |
| onerror                 | ❌           |
| quality                 | ❌           |
| rotate                  | ✅           |
| saturation              | ❌           |
| segment                 | ❌           |
| sharpen                 | ❌           |
| slow-connection-quality | ❌           |
| trim                    | ❌           |
| width                   | ✅           |
| zoom                    | ❌           |

### How to use it

Run it using docker:
```
docker compose up -d
```

After that service will be available on port 80 (or value passed to environment variable `RESIZLY_PORT`).
Usage:
```
http://localhost/[PARAMETERS]/[URL_TO_IMAGE]
```
For example:
```
http://localhost/width=200,height=200,fit=cover/https://i.imgur.com/0GZkiA6.png
```
