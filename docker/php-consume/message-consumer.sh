#!/usr/bin/env bash
sleep 10;
/usr/share/nginx/html/bin/console messenger:consume -vv >&1;
