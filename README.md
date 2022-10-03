# Small Scheduler Client

Small Scheduler Client is a task executor for Small Scheduler.

You can install it in your cloud servers to listen for a task to execute.

## Make package

``` bash
$ apt-get update && apt-get install docker docker-compose
$ git clone git@github.com:sebk69/small-scheduler-client.git
$ cd small-scheduler-client && docker-compose up -d --build
```

The debian package is here : small-scheduler-client.deb

## Installation

For now only Debian based distributions are supported.

For Ubuntu distributions Ubuntu 12.04 to Ubuntu 18.10 are supported.

First install php (versions from 5.2 to 7.2 are supported) and curl
``` bash
$ sudo apt-get install php curl
```

Then download the deb package (You can download it from tag binaries) and install it :
``` bash
$ sudo dpkg -i small-scheduler-client.deb
```

Then edit config file at /etc/small-scheduler.json (See below for configuration options)

When you are ready just start service :
``` bash
$ sudo small-scheduler-client start
```

To stop service :
``` bash
$ sudo small-scheduler-client stop
```

If the process fail hard reboot for example you must start forced to bypass lock file :
``` bash
$ sudo small-scheduler-client start --force
```

## Configuration

The configuration file is in json format.

The location of config file is : /etc/small-scheduler.json

Here is default configuration :
``` json
{
    "server": {
        "ip": "127.0.0.1",
        "port": 5672,
        "user": "guest",
        "password": "guest"
    },
    "workers": [
        {
            "queue": 1,
            "number": 1
        }
    ]
}
```

### "server" section

It is the definition of "how to connect" to Small Scheduler server.

- **ip** : the ip address or url of server (or service name in Kubernetes infrastructure).
- **port** : common is 5672 (the standard port for RabbitMq queue listening port) but it can be changed in server installation
- **user** : RabbitMq user in Small Scheduler server
- **password** : RabbitMq password in Small Scheduler server

### "workers" section

This section define workers that can execute job.

There is one object entry per queue.

- **queue** : The queue number to listen
- **number** : The number of parallel job can be executed on this server for the queue

# Credits
Sébastien Kus

# Licence
This software is under GNU GPL 3 LICENSE.

For the whole copyright, see the LICENSE file distributed with this source code.
