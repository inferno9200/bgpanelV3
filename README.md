# BGP V3

# Installation

1. **`Install docker`**
2. `mkdir -p /root/workdir/; cd /root/workdir`
3. `wget https://raw.githubusercontent.com/DopeProjects/bgpanelV3/master/docker/docker-compose.yml`
4. **Open `docker-compose.yml` and replace `192.168.1.101` with your private ipv4**
5. `docker-compose -f /root/workdir/docker-compose.yml up -d`
