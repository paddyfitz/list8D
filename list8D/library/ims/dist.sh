rm dist.zip /tmp/dist.zip
zip -r /tmp/dist.zip * -x \*.svn\*
cp /tmp/dist.zip .
