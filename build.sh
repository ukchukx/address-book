#!/bin/bash
composer du
npm run prod
docker build --tag address-book .
