FROM gitpod/workspace-mysql

RUN echo "composer-link() {composer config repositories.local '{\"type\": \"path\", \"url\": \"'$1'\"}' --file composer.json}" >> ~/.bash_rc


