FROM registry.netresearch.de/magento-ci/base:1.9.3.1
MAINTAINER Christoph Aßmann <christoph.assmann@netresearch.de>

RUN wget https://raw.githubusercontent.com/netresearch/retry/master/retry -O /usr/local/bin/retry && \
    chmod +x /usr/local/bin/retry

VOLUME ["/var/www/mageone.nrdev.de/html", "/var/www/mageone.nrdev.de/vendor"]

COPY run.sh /run.sh
RUN chmod u+rwx /run.sh

WORKDIR /var/www/mageone.nrdev.de

CMD ["mysql","3307","MagEC1"]
ENTRYPOINT ["/run.sh"]
