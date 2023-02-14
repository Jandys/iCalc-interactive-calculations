FROM alpine

# Install zip package
RUN apk add --no-cache zip

RUN mkdir /home/src && mkdir /deploy
RUN mkdir /mnt/system && mkdir /mnt/system/depoy
# Copy the source files into the container
COPY src/* /home/src


# Create a compressed archive of the source files using zip
RUN cd /home/src && zip -r /deploy/icalc.zip .
# Set the working directory
WORKDIR /deploy
ENTRYPOINT cp /deploy/icalc.zip /mnt/system/deploy &&  rm -rf /deploy/* && rm -rf /home/src && chmod o+wr /mnt/system/deploy -R
