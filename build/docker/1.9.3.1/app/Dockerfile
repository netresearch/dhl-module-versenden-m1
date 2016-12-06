FROM registry.netresearch.de/magento-ci/base:1.9.3.1
MAINTAINER Christoph Aßmann <christoph.assmann@netresearch.de>

VOLUME ["/var/www/mageone.nrdev.de/html", "/var/www/mageone.nrdev.de/vendor"]

COPY run.sh /run.sh
RUN chmod u+rwx /run.sh

WORKDIR /var/www/mageone.nrdev.de
CMD ["/run.sh"]

