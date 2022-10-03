FROM debian:latest

RUN apt-get update && \
        apt-get install -y dpkg

ENTRYPOINT cd /tmp && \
        dpkg-deb --build small-scheduler-client && \
        cp /tmp/small-scheduler-client.deb /tmp/small-scheduler-client/small-scheduler-client.deb