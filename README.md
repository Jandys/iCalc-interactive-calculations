# iCalc - Interactive Calculation WordPress Plugin

![iCalc - Interactive Calculation WordPress Plugin](https://php.jandys.eu/wp-content/uploads/2023/05/iCalc-Small.png)

iCalc is a powerful and easy-to-use interactive calculation plugin for WordPress websites. It allows you to create
beautiful and responsive calculators, giving your users the power to make instant calculations on your site. From simple
arithmetic to complex formulas, iCalc can handle it all. Additionally, it comes with built-in support for running inside
Docker containers, making deployment a breeze!

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [How to Use](#how-to-use)
- [Running in a Docker Container](#running-in-a-docker-container)
- [Support and Contributions](#support-and-contributions)
- [License](#license)

## Features

- Easy-to-use calculator builder with a drag-and-drop interface
- Reuse your defined components.
- Get data from users interactions.
- Customizable styling to match your website's theme
- Seamless integration with WordPress and popular page builders
- Support for running in a Docker container

## Installation

1. Download the iCalc plugin from the [GitHub repository](https://github.com/Jandys/iCalc-interactive-calculations/blob/release-archives/release.zip ).
2. Log in to your WordPress dashboard and navigate to **Plugins** > **Add New**.
3. Click **Upload Plugin** and select the downloaded iCalc zip file.
4. Click **Install Now** and wait for the installation to complete.
5. Activate the iCalc plugin by clicking **Activate Plugin**.

## How to Use

1. Navigate to your WordPress dashboard and click **iCalc** in the left-hand menu.
2. Create a new calculator by clicking **Add New Calculator**.
3. Use the drag-and-drop interface to design your calculator layout and configure its functions.
4. Save your calculator and copy the generated shortcode.
5. Add the shortcode to your desired post, page, or widget area.

## Running in a Docker Container?

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

## Support and Contributions

If you encounter any issues or would like to contribute to the project, please file an issue on
the [GitHub repository](https://github.com/Jandys/iCalc-interactive-calculations/issues) or submit a pull request.

## License

iCalc is released under the [GNU GPLv3 License](https://www.gnu.org/licenses/gpl-3.0.en.html). See [LICENSE](LICENSE)
for more
information.
