

# SimpleSlide

[![s.io/github/v/GitHub release (latest by date including pre-releases)](https://img.shields.io/github/v/release/jaycollett/simpleslide?include_prereleases)](https://img.shields.io/github/v/release/jaycollett/simpleslide?include_prereleases)
[![GitHub last commit](https://img.shields.io/github/last-commit/jaycollett/simpleslide)](https://img.shields.io/github/last-commit/jaycollett/simpleslide)
[![GitHub issues](https://img.shields.io/github/issues-raw/jaycollett/simpleslide)](https://img.shields.io/github/issues-raw/jaycollett/simpleslide)
[![GitHub pull requests](https://img.shields.io/github/issues-pr/jaycollett/simpleslide)](https://img.shields.io/github/issues-pr/jaycollett/simpleslide)
[![GitHub](https://img.shields.io/github/license/jaycollett/simpleslide)](https://img.shields.io/github/license/jaycollett/simpleslide)

A simple image slideshow Docker image. 

This project was developed after I struggled to find a simple way to show some images through HTTP in a slide show. I was shocked that the solutions I already owned/hosted couldn't manage such a simple task, so I built my own because, well, why not?

The primary use case for this solution is to serve some locally stored family pictures as a slide show on my various Home Assistant tablets running Fully Kiosk software. The Fully Kiosk ScreenSaver solution really wants a simple HTTP URL for slideshow images or an undocumented JSON list of static images; I wanted to be able to re-use a storage folder I already had shared, have the images rotate (slide), be able to adjust how fast they progress, and lastly, I wanted the solution to update periodically to pick up new images I place on the share I map to the container. 

The container is super easy to get running, the only required docker arguments are the port mapping, if you don't want to run it the default port 80 and the volume mapping. The volume mapping is super critical, make sure your images are sitting in the root of the local path and that you don't alter the mapping to the /var/www/html/images path.

**Docker CLI**

    docker run -dit \
      --name=simpleslide \
      -e PUID=1000 \
      -e PGID=1000 \
      -e TZ=Etc/UTC \
      -e delayinsecs=60 \
      -p 8181:80 \
      -v /path/to/images:/var/www/html/images \
      --restart unless-stopped \
      ghcr.io/jaycollett/simpleslide:latest
      
Now you should be able to access your SimpleSlide image instance at: http://IPAddressOfHost:8181/
