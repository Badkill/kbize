Kbize
=====

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/silvadanilo/kbize?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

[![Build Status](https://travis-ci.org/silvadanilo/kbize.svg?branch=master)](https://travis-ci.org/silvadanilo/kbize)

What is it?
-----------
kbize is a simple php command to operate with your kanbanize board on a unix shell.

- display a list of task filtered by custom filters
- add a task
- move a task
- delete a task

Kbize application is based on Symfony console.

How do I get started?
---------------------

You can use Kbize in one of two ways:

### As a Phar (Recommended)

You may download a ready-to-use version of Kbize as a Phar:

```sh
$ curl -LSs http://silvadanilo.github.io/kbize/installer.php | php
```

The command will check your PHP settings, warn you of any issues, and the download it to the current directory. From there, you may place it anywhere that will make it easier for you to access (such as `/usr/local/bin`) and chmod it to `755`. You can even rename it to just `kbize` to avoid having to type the `.phar` extension every time.

```sh
$ kbize --version
```

Whenever a new version of the application is released, you can simply run the `update` command to get the latest version:

```sh
$ kbize self:update
```
### Git project
......
