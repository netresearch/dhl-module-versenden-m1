FROM couchi.nr:5001/magento-ci/base:1.7.0.2
MAINTAINER Christoph Aßmann <christoph.assmann@netresearch.de>

VOLUME ["/var/www/html", "/var/www/vendor"]

COPY run.sh /run.sh
RUN chmod u+rwx /run.sh

WORKDIR /var/www
CMD ["/run.sh"]

