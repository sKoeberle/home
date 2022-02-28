#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# Import
import os
import sys
import time
import _ssl
import socket
import mariadb
import configparser
import RPi.GPIO as GPIO
from papirus import PapirusComposite
from pijuice import PiJuice
from time import gmtime, localtime, strftime
from PIL import ImageFont

# Import config
Config = configparser.ConfigParser()
Config.read('config.cfg')
Config.sections()
'Db' in Config
Host = Config['Db']['Host']
Port = int(Config['Db']['Port'])
User = Config['Db']['User']
Pwd = Config['Db']['Pwd']
Name = Config['Db']['Name']

# Init Variables
global pijuice, bDisplayTime, timer, sTitle1, sTitle2, sTitle3
pijuice = PiJuice(1, 0x14)
bDisplayTime = False
timer = 1
sTitle1 = 'Thermostat'
sTitle2 = 'Display'
sTitle3 = 'v1.10'
sMainFontPath = 'font/conthrax-sb.ttf'


# Init the GPIO
def init_gpio():
    GPIO.setwarnings(False)
    GPIO.setmode(GPIO.BCM)
    GPIO.setup(16, GPIO.IN)
    GPIO.setup(26, GPIO.IN)
    GPIO.setup(20, GPIO.IN)
    GPIO.setup(21, GPIO.IN)

    GPIO.add_event_detect(16, GPIO.RISING, callback=reboot_pi, bouncetime=75)
    GPIO.add_event_detect(20, GPIO.RISING, callback=wifi_switch, bouncetime=75)
    GPIO.add_event_detect(21, GPIO.RISING)
    GPIO.add_event_detect(26, GPIO.RISING, callback=refresh_screen, bouncetime=75)


# Test LAN connect
def is_connected(hostname):
    try:
        # see if we can resolve the host name -- tells us if there is a DNS listening
        host = socket.gethostbyname(hostname)
        # connect to the host -- tells us if the host is actually reachable
        s = socket.create_connection((host, 80), 2)
        s.close()
        return True
    except:
        pass
    return False


# Get PiJuice status
def get_pijuice_plug_status():
    status = pijuice.status.GetStatus()
    status = status['data']['powerInput']
    return status


# Get PiJuice charge level
def get_pijuice_battery_level():
    level = pijuice.status.GetChargeLevel()
    level = level['data']
    return level


# Get current temperature of the main sensor
def get_current_temperature():
    temperature = 0
    conn = mariadb.connect(host=Host, user=User, password=Pwd, database=Name, port=Port)
    cursor = conn.cursor()
    cursor.execute(
        "SELECT `value` FROM `sensors` WHERE `type` = 'temperature' AND `location` = 'living-room' ORDER BY `recordTime` DESC LIMIT 0,1")
    for (temp) in cursor:
        temperature = temp[0]
    cursor.close()
    conn.close()
    return round_to_five(temperature)


# Get current humidity of the main sensor
def get_current_humidity():
    humidity = 0
    conn = mariadb.connect(host=Host, user=User, password=Pwd, database=Name, port=Port)
    cursor = conn.cursor()
    cursor.execute(
        "SELECT `value` FROM `sensors` WHERE `type` = 'humidity' AND `location` = 'living-room' ORDER BY `recordTime` DESC LIMIT 0,1")
    for (temp) in cursor:
        humidity = temp[0]
    cursor.close()
    conn.close()
    return round_to_five(humidity)


# Round to 5
def round_to_five(nb):
    return round(nb * 2) / 2


# Get Text Size
def get_text_size(text, fontpath, size):
    font = ImageFont.truetype(fontpath, size)
    return int(font.getsize(text)[0])


# Refresh whole screen
def refresh_screen(channel):
    screen = PapirusComposite(False, 0)
    screen.Clear()


# Return icon to display for battery level
def get_battery_icon_level(level):
    if level >= 75:
        return 'image/Battery-100.bmp'
    if 75 > level >= 25:
        return 'image/Battery-50.bmp'
    if 10 <= level < 25:
        return 'image/Battery-0.bmp'
    if 0 < level < 10:
        return 'image/Battery-Plug.bmp'
    if level == -1:
        return 'image/Lightning.bmp'


# Display time
def display_time(channel):
    bDisplayTime = True
    screen = PapirusComposite(False, 0)
    # screen.Clear()
    sDate = strftime('%d %B %Y', localtime())
    sTime = strftime('%H:%M', localtime())
    screen.AddText(sDate, (264 - get_text_size(sDate, sMainFontPath, 16)) / 2, 104, size=16, Id='Date',
                   fontPath=sMainFontPath)
    screen.AddText(sTime, (264 - get_text_size(sTime, sMainFontPath, 64)) / 2, 40, size=64, Id='Time',
                   fontPath=sMainFontPath)
    screen.WriteAll(True)
    time.sleep(5)
    return bDisplayTime


# Wifi switch on/off
def wifi_switch(channel):
    ipaddress = ""

    if is_connected(Host):
        os.system("sudo ifconfig wlan0 down")
    else:
        os.system("sudo ifconfig wlan0 up")

        while ipaddress == "":
            ipaddress = os.popen("ifconfig wlan0 | grep 'inet ' | awk -F: '{print $1}' | awk '{print $2}'").read()
            time.sleep(1)

        screen = PapirusComposite(False, 0)
        screen.AddText(ipaddress, (264 - get_text_size(ipaddress, sMainFontPath, 32)) / 2, 60, size=32, Id='Time',
                       fontPath=sMainFontPath)
        screen.WriteAll(True)
        time.sleep(2)
        return


# Reboot pi
def reboot_pi(channel):
    screen.Clear()
    os.system('sudo reboot')


# Init GPIO
init_gpio()

# Display splash screen
screen = PapirusComposite(False, 0)
screen.AddText(sTitle1, (264 - get_text_size(sTitle1, sMainFontPath, 32)) / 2, 40, size=32, Id='Title',
               fontPath=sMainFontPath)
screen.AddText(sTitle2, (264 - get_text_size(sTitle2, sMainFontPath, 32)) / 2, 70, size=32, Id='Title2',
               fontPath=sMainFontPath)
screen.AddText(sTitle3, (264 - get_text_size(sTitle3, sMainFontPath, 16)) / 2, 110, size=16, Id='Title3',
               fontPath=sMainFontPath)
screen.WriteAll()
time.sleep(2)

# First read
sCurrentTemp = str(get_current_temperature())
sCurrentHumidity = str(get_current_humidity())

# Main loop
while True:

    if GPIO.event_detected(21):
        bDisplayTime = display_time(21)

    # CurrentTemp = str(get_current_temperature()) + "°C"
    t = timer / 30
    if t.is_integer() == True:
        sCurrentTemp = str(get_current_temperature())
        sCurrentHumidity = str(get_current_humidity())

    powerStatus = get_pijuice_plug_status()

    if powerStatus == 'PRESENT':
        sBatteryIcon = get_battery_icon_level(-1)
    else:
        sBatteryIcon = get_battery_icon_level(int(get_pijuice_battery_level()))

    screen = PapirusComposite(False, 0)

    # screen.AddText("Temperature", 10, 10, size=24)
    # text.AddText(sCurrentTemp, 10, 30, size=48, Id="Temp")
    # screen.AddText(sCurrentTemp, 10, 34, size=60, Id="Temp", fontPath='digital_7_mono.ttf')
    # screen.AddText("Humidity", 10, 80, size=24)
    # text.AddText(sCurrentHumidity, 10, 100, size=48, Id="Humid")
    # screen.AddText(sCurrentHumidity, 10, 104, size=60, Id="Humid", fontPath='digital_7_mono.ttf')
    # screen.AddText(sBatteryLevel, PosX, 5, size=20, Id="BattLevel")

    # screen.AddText(str(is_connected(Host)), 0 , 0, Id='Test')

    # Is LAN connected?
    if is_connected(Host):
        screen.AddImg('image/Wifi.bmp', 208, 4, (30, 24), Id='WifiIcon')

    # Construct display
    screen.AddImg(sBatteryIcon, 236, 1, (32, 32), Id='BattIcon')
    screen.AddText(sCurrentTemp, 60, 40, size=56, Id="Temp", fontPath=sMainFontPath)
    screen.AddText('°C', 64 + get_text_size(sCurrentTemp, sMainFontPath, 56), 62, size=32, Id='TempUnit',
                   fontPath=sMainFontPath)
    screen.AddText(sCurrentHumidity, 60, 110, size=56, Id="Humid", fontPath=sMainFontPath)
    screen.AddText('%', 64 + get_text_size(sCurrentHumidity, sMainFontPath, 56), 132, size=32, Id='HumidUnit',
                   fontPath=sMainFontPath)
    screen.AddImg('image/Temperature_56.bmp', 0, 42, (56, 56), Id='TempImg')
    screen.AddImg('image/Drop_56.bmp', 0, 112, (56, 56), Id='DropImg')

    # Refresh whole screen after time displayed or 5 min
    if timer >= 300 or bDisplayTime == True:
        timer = 0
        bDisplayTime = False
        screen.WriteAll()
    else:
        screen.WriteAll(True)

    timer += 1
    time.sleep(1)
