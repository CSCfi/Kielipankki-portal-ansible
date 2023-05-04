# The Language Bank Portal Installer

This directory contains two ansible recipes for creating a new version of the Portal

 * portalPouta.yml (for portal-pre-prod VMs)


# Prerequisites

You need

 - Access to cPouta
 - Access to the Kielipankki shared passwords in purkki, see: https://wiki.csc.fi/Kielipankki/CredentialManagement
 - Write access to https://github.com/CSCfi/Kielipankki/
 - A recent backup of the portal from korp:/var/backup : DOW-kielipankki_portal_backup.tar.gz. DOW is a three letter abbreviation of the present day, use `LC_ALL=C date +%a` to determine it. If your backup is from the day before you must rename it.


Edit the Ansible script below servers/portal/.

#  Deploying the Portal to cPouta

## Pre-requisites
- Ansible >=2.5: https://docs.ansible.com/ansible/latest/installation_guide/index.html
- Python 3: Needed by the OpenStack command line tools
- OpenStack [command line tools](https://docs.openstack.org/newton/user-guide/common/cli-install-openstack-command-line-clients.html) (also see [cPouta User guide](https://docs.csc.fi/cloud/pouta/command-line-tools/). Note: using apt or yum is not recommended, the tools available might be too old.
  - An easy way to install the tools is to install the tools within a virtual environment:
    ```
    virtualenv -p python3 .venv
    source .venv/bin/activate
    pip install -r requirements_dev.txt
    ```
    If there were no errors and `openstack -h` produces help output, everything likely works. Remember to activate the virtual environment every time you wish to use the tools in a new session.
- [Shade](http://docs.openstack.org/infra/shade/) (already installed if you followed the instructions above)- Access to pouta: https://docs.csc.fi/#accounts/how-to-add-service-access-for-project/
- [Access to pouta](https://docs.csc.fi/accounts/how-to-add-service-access-for-project/)
- Your cPouta project's OpenStack RC file: https://docs.csc.fi/#cloud/pouta/install-client/#configure-your-terminal-environment-for-openstack
- Your cPouta project's PEM certificate downloaded from https://pouta.csc.fi/dashboard/project/access_and_security/

- Ansible >=2.5: https://docs.ansible.com/ansible/latest/installation_guide/index.html



- Your cPouta project's [OpenStack RC file](https://docs.csc.fi/cloud/pouta/install-client/#configure-your-terminal-environment-for-openstack)
- Key pair for cPouta instances. Created in https://pouta.csc.fi/ (Project > Compute > Key Pairs) and must be named "kielipouta".

## Clone the Language Portal Git project

You can find the Ansible playbooks and roles needed for deploying the Languag Portal in cPouta under `Kielipankki/servers/portal`

```
$ git clone https://github.com/CSCfi/Kielipankki.git
$ cd Kielipankki/servers/portal`
```


## Source your cPouta (OpenStack) auth file.

The [OpenStack auth file](https://docs.csc.fi/#cloud/pouta/install-client/#configure-your-terminal-environment-for-openstack) is necessary for provisioning the OpenStack resources. 

`$ source project_2000680-openrc.sh`

See [Configure your terminal environment for OpenStack](https://docs.csc.fi/cloud/pouta/install-client/#configure-your-terminal-environment-for-openstack) for details.


## Run Ansible

Run the provisioning playbook.

`$ ansible-playbook portalPouta.yml`

This command should create and configure the pre-production version of
the Portal in cPouta. To find out the IP of the VM login to the
[cPouta Web interface](https://pouta.csc.fi/dashboard/), select the
correct project (presently 2000680) and look for the portal-pre-prod Instance.

## Testing

The just created instance needs a proxy in front of is to work
properly (the proxy handles for examle HTTPS.) See
../kielipankki-proxy/README.md for details.

To test your portal-pre-prod instance, edit your /etc/hosts file in Linux so that
www.kielipankki.fi and
kielipankki.fi point to the IP of the kielipankki-proxy-pre-prod. (Note: Use the IP of the proxy, not the IP of the portal instance.)


# PRODUCTION: Updating the Portal in production

Only perform this step once you are happy with the "pre-prod" instance created in cPouta.

## Prerequisites

 - The same as above

## Get/Create the most recent backup

To create an immediate backup from production:

 * Make sure the staging (`portal-pre-prod`) and production (`portal-prod`) IP addresses are correctly set in `inventories/openstack_portal_pre_prod`. 
 * Run ansible-playbook -i inventories/openstack_portal_pre_prod portalPouta.yml -t get_fresh_backup

## Prepare the pre-production (staging) server

Re-Install the changes to staging. The menu background will be red to
mark staging. It is recommended to add "--extra-vars clean=true" for
the first run to completely reinstall wordpress.  "clean=true" wipes
the whole /var/www/html directory. This might be overkill in some
situations.

 - `cd servers/portal`
 - `ansible-playbook -i inventories/openstack_portal_pre_prod portalPouta.yml --extra-vars clean=true`

## Optional: Resync content between present production and pre-production

If someone has changed content on production during testing of
pre-production, get a fresh backup copy again (see above) and
sync the content only using the *update_wp_content*
tag:

 - `ansible-playbook -i inventories/openstack_portal_pre_prod portalPouta.yml -t get_fresh_backup -t update_wp_content`

This assumes that the pre-production server is otherwise ready and only needs a content sync.


## Switch to the new version

 - On pre-production run as root: 
  -``sudo -u apache /usr/local/bin/wp config set WP_DEBUG false --raw --path=/var/www/html``
  - ``sudo -u apache /usr/local/bin/wp super-cache flush --path=/var/www/html``
 - Make sure the menu background is now black.
 - login to the proxy: ("kielipankki-proxy-prod": ssh cloud-user@195.148.30.210) 
 - change the proxy settings in /etc/httpd/conf.d/ssl.conf and point to the pre-production server's INTERNAL IP (7/2020 that is: 192.168.1.8). The new server is immediately in use and now considered production.
 - Important: RENAME the now production server manually in the cPouta dashboard from portal-pre-prod to portal-prod (or portal-prod2, if "portal-prod" is already in use. This prevents future ansible runs from changing the now renamed "portal-pre-prod".
 - 
ansible-playbook -i inventories/openstack_static_production proxyPouta.yml -t apache_config_update

