**BIG FAT WARNING**
===
This, and other required Pi-Star modules, are my personal forks, **DO NOT** report bugs, request features or anything like that, to the official **Pi-Star** developer, **Andy Taylor** (MW0MWZ), on the Pi-Star's Facebook page or on the forum.

***

**How to install it**

* You need a working Pi-Star, from [here](http://www.pistar.uk/downloads/). Pi-Star 3 and 4 are supported.

* **MAKE A BACKUP OF YOUR Pi-Star CONFIGURATION**
* Connect your Pi-Star
```shell
ssh pi-star@pi-star.local
```
* Grab the script that permits you to toggle between official repositories and my personnal ones.
```shell
sudo su
rpi-rw
cd
wget http://tinyurl.com/f1rmb-pistar-ng -O f1rmb-pistar
chmod +x f1rmb-pistar
```
* Now you can execute this script with some arguments. For a complete list, use '-h' or '--help'
The easiest way:
	* To install these forks repositories:
	```shell
	./f1rmb-pistar -ia
	```
	* To switch back to the official ones:
	```shell
	./f1rmb-pistar -ra
	```

***

1. Once you have installed this fork, you need to go in menu "**Configuration**" -> "**Expert**" -> "**Tools**" and select "**CSS Tool**" 
This will reset all the colors to their default value.
2. You will need to launch "pistar-upgrade" multiple times, until it displays the "**You are already running the latest version..**" message.

***

**What features this fork offers**

* An enhanced POCSAG support. A service and network status indicators are there, You can send pages from the **Admin** web page. When you receive personnal pages, they are extracted from the **Activity** and displayed in a dedicated table. You can send a page to multiple callsigns and/or transmitter groups, separated with comma:
![POCSAG](images/Dapnet_Messenger.png  "POCSAG")

* A new menu system, cleaner, nicer:
![Expert Menus](images/Expert_Menus.png  "Expert Menus")

* An easy and extended way to change the color theme (using the farbtastic plugin):
![Farbtastic Color Picker](images/CSS_ColorPicker.png  "Farbtastic Color Picker")
![Gray Colors](images/Color2.png  "Gray Colors")
![Orange Colors](images/Color3.png  "Orange Colors")

* Code optimization and cleanups.

* Gateway and DAPNet Activity (*last heard*) tables are extended to the 40 last entries, fitted in a scrolling window.

* Integration of [Tiny File Manager](https://github.com/prasathmani/tinyfilemanager).

* GPSd support.

* Full integration of NextionDriver.

* Complete support of configuration files edition.

* TheShield and alike (Libre DMR) support.

* Support of latest G4KLX software suite (one gateway per mode, DMR Master connections handled by DMRGateway only, etc...).

* Pi-Star services management integrated in the web interface

* ***pistar-cli*** and ***pistar-services*** scripts added
   - it's possible to use ***pistar-cli*** with [Raspi Check](https://github.com/eidottermihi/rpicheck) ([Google Play](https://play.google.com/store/apps/details?id=de.eidottermihi.raspicheck&hl=en&gl=US)) to switch the hostpot's configuration on the fly.

* Continuous backport of upstream's relevant fixes and improvments.

* Tons of small modifications and tweaks that can't be enumerated here.
