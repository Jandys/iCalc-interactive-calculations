# Inter Calcus WordPress (Production Title)

Is a WordPress plugin that is used for interactive calculations for free use.

### Do you run in container?

If you run in container you may run into issues due to Plugin trying to call itself. But you may have disabled listening
on this port.
Don't worry just run this command inside your container with Wordpress and apache.

```shell

echo -e "\nListen 8080\n" >> /etc/apache2/ports.conf
echo -e "\n<VirtualHost *:*>\n</VirtualHost>\n" >> /etc/apache2/sites-available/000-default.conf

cat /etc/apache2/ports.conf && cat /etc/apache2/sites-available/000-default.conf
```

This would only work if your Wordpress is exposed on port **8080**. If you deploy your container with different port,
you need to adjust the script to your port.  


