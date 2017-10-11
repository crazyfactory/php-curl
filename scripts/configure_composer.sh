#!/usr/bin/env bash

# configure composer
cat > ${HOME}/.composer/config.json <<EOF
{
  "repositories": [
    {
      "type": "composer",
      "url": "https://php.fury.io/${GEMFURY_KEY}/crazyfactory/"
    }
  ]
}
EOF
