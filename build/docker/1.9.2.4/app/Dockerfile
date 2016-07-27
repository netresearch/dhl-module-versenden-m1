FROM couchi.nr:5001/magento-ci/base:1.9.2.4
MAINTAINER Christoph Aßmann <christoph.assmann@netresearch.de>

ENV DEBIAN_FRONTEND=noninteractive

VOLUME ["/var/www/html", "/var/www/vendor"]

COPY run.sh /run.sh
RUN chmod u+rwx /run.sh

WORKDIR /var/www
CMD ["/run.sh"]

