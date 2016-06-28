FROM couchi.nr:5001/magento-ci/base:1.7.0.2
MAINTAINER Christoph Aßmann <christoph.assmann@netresearch.de>

ENV DEBIAN_FRONTEND=noninteractive

VOLUME ["/var/www/htdocs"]

COPY run.sh /run.sh
RUN chmod u+rwx /run.sh

WORKDIR /var/www
CMD ["/run.sh"]

