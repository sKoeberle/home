#!/usr/bin/python

# import cgitb
import os
import cgi
import time
import mysql.connector
import RPi.GPIO as GPIO

# cgitb.enable()

DEBUG = False


# Init
def init():
    global mode, ambiance_mode, target_temp, reduced_temp, current_temp, current_mode, temperature, daily_programming_mode


# Init the GPIO
def init_gpio():
    GPIO.setwarnings(False)
    GPIO.setmode(GPIO.BCM)
    GPIO.setup(22, GPIO.OUT)
    GPIO.setup(23, GPIO.OUT)
    GPIO.setup(24, GPIO.OUT)
    GPIO.setup(25, GPIO.OUT)


# Reset the GPIO
def reset_gpio():
    circulator("off")


class Color:
    def __init__(self):
        pass

    PURPLE = '\033[95m'
    CYAN = '\033[96m'
    DARKCYAN = '\033[36m'
    BLUE = '\033[94m'
    GREEN = '\033[92m'
    YELLOW = '\033[93m'
    RED = '\033[91m'
    BOLD = '\033[1m'
    UNDERLINE = '\033[4m'
    END = '\033[0m'


# Manage the circulator
def circulator(value):
    if value == "on":
        GPIO.output(22, 0)
        print('The pump is running')
    else:
        GPIO.output(22, 1)
        print('The pump is stopped')


# Manage the three-way valve
# Need 2m40s (160s) for opening or closing from 0 to 100%
# So 16s per step of 10%
def valve(value):
    if value == "open":
        print('Three-way valve opening')
        GPIO.output(24, 1)
        GPIO.output(23, 0)
        sleeper(16)
        GPIO.output(23, 1)
    if value == "close":
        print('Three-way valve closing')
        GPIO.output(23, 1)
        GPIO.output(24, 0)
        sleeper(16)
        GPIO.output(24, 1)


# Get the target temperature set by the user
def get_target_temperature():
    conn = mysql.connector.connect(host="192.168.1.251", user="home", password="2DsNEPnDHH93WT2y", database="home")
    cursor = conn.cursor()
    cursor.execute("""SELECT `temperature` FROM `target` ORDER BY `demand` DESC LIMIT 0,1""")
    for (temp) in cursor:
        temperature = temp[0]
    cursor.close()
    conn.close()
    return temperature


# Get current temperature of the main sensor
def get_current_temperature():
    conn = mysql.connector.connect(host="192.168.1.251", user="home", password="2DsNEPnDHH93WT2y", database="home")
    cursor = conn.cursor()
    cursor.execute(
        """SELECT `value` FROM `sensors` WHERE `type` = 'temperature' AND `location` = 'living-room' ORDER BY `recordTime` DESC LIMIT 0,1""")
    for (temp) in cursor:
        temperature = temp[0]
    cursor.close()
    conn.close()
    return temperature


# Get the current ambiance mode in database for display
def get_ambiance_mode(ambiance_mode):
    conn = mysql.connector.connect(host="192.168.1.251", user="home", password="2DsNEPnDHH93WT2y", database="home")
    cursor = conn.cursor()
    cursor.execute("""SELECT `value` FROM `general` WHERE `label` = 'ambiance_mode'""")
    for (val) in cursor:
        ambiance_mode = val[0]
    cursor.close()
    conn.close()
    return ambiance_mode


# Get the current daily programming mode
def get_current_daily_programming_mode():
    conn = mysql.connector.connect(host="192.168.1.251", user="home", password="2DsNEPnDHH93WT2y", database="home")
    cursor = conn.cursor()
    cursor.execute("""SELECT `value` FROM `general` WHERE `label` = 'dailyProgrammingMode'""")
    for (val) in cursor:
        daily_programming_mode = val[0]
    cursor.close()
    conn.close()
    return daily_programming_mode


# Get the current daily programming mode
def get_hour_mode(daily_programming_mode, hour):
    conn = mysql.connector.connect(host="192.168.1.251", user="home", password="2DsNEPnDHH93WT2y", database="home")
    cursor = conn.cursor()
    # print "SELECT `%s` FROM `program` WHERE `day` = '%s'""" % (hour, daily_programming_mode)
    cursor.execute("""SELECT `%s` FROM `program` WHERE `day` = '%s'""" % (hour, daily_programming_mode))
    for val in cursor:
        daily_programming_mode = val[0]
    cursor.close()
    conn.close()
    return daily_programming_mode


# Set the current ambiance mode in database for display
def set_current_ambiance_mode(mode):
    conn = mysql.connector.connect(host="192.168.1.251", user="home", password="2DsNEPnDHH93WT2y", database="home")
    cursor = conn.cursor()
    cursor.execute("""UPDATE `general` SET `value` = %s WHERE `label` = %s""", (mode, 'currentAmbianceMode'))
    cursor.close()
    conn.close()


# Regulate temperature
def regulation(current_temp, target_temp):
    print('Target temp is %sC' % target_temp)
    print('Current temp is %sC' % current_temp)
    print('\r')

    diff_temp = current_temp - target_temp
    diff_temp = round(diff_temp, 1)
    diff_temp = abs(diff_temp)

    if diff_temp <= 0.2:
        multiplicator = 0
        current_temp = target_temp
    else:
        multiplicator = int(round(diff_temp, 0))
        if multiplicator == 0:
            multiplicator = 1

    if DEBUG:
        print('diff_temp is %s' % diff_temp)
        print('multiplicator is %s' % multiplicator)

    if current_temp > target_temp:
        print('Temperature is too high!')
        for i in range(0, multiplicator):
            valve("close")
    if current_temp < target_temp:
        print('Temperature is too low!')
        for i in range(0, multiplicator):
            valve("open")
    if current_temp == target_temp:
        print('Temperature is nice, no changes necessary.')


# Make a pause
def sleeper(value):
    num = float(value)
    time.sleep(num)


# Initialisation
init()
init_gpio()

# Manage temperatures
target_temp = get_target_temperature()
current_temp = get_current_temperature()
reduced_temp = target_temp - 3
if reduced_temp < 17:
    reduced_temp = 17

# Manage program
ambiance_mode = get_ambiance_mode('auto')
current_mode = ''
daily_programming_mode = ''
if ambiance_mode == 'auto':
    daily_programming_mode = get_current_daily_programming_mode()
    day = time.strftime('%w')
    hour = time.strftime('%H')
    minute = time.strftime('%M')
    t = int(hour)
    if minute >= '30':
        t = "%.2f" % float(time.strftime('%H') + '.30')

    if daily_programming_mode == 'everyday':
        current_mode = get_hour_mode('all', t)
    elif daily_programming_mode == 'weekday':
        if day == '0' or day == '6':
            current_mode = get_hour_mode('weekend', t)
        else:
            current_mode = get_hour_mode('weekday', t)
    elif daily_programming_mode == 'eachday':
        if day == '0':
            day = '1'
        if day == '6':
            day = '7'
        current_mode = get_hour_mode(day, t)

# Display current time
print('\r')
print('----------------------------')
print time.strftime('It is %A, %H:%M\n')
print('Ambiance mode is set on %s' % ambiance_mode)
# print('Time is %s' % t)
# print('Current boiler mode is %s' % current_mode)
print('Daily programming mode is %s' % daily_programming_mode)
# print('\n')


# Manage ambiance mode
# if ambiance_mode == 'auto':
#    if time.strftime('%H') in ['23', '00', '01', '02', '03', '04', '05']:
#        print('The thermostat is in reduced mode')
#        print('The reduced temp is %s' % reduced_temp)
#        target_temp = reduced_temp
#        set_current_ambiance_mode('reduced')
#        if current_temp > target_temp:
#            circulator("off")
#        else:
#            circulator("on")
#            regulation(current_temp, target_temp)
#    else:
#        print('The thermostat is in comfort mode')
#        set_current_ambiance_mode('comfort')
#        circulator("on")
#        regulation(current_temp, target_temp)
# elif ambiance_mode == 'comfort':
if current_mode == 1:
    print('The thermostat is in comfort mode')
    set_current_ambiance_mode('comfort')
    circulator("on")
    regulation(current_temp, target_temp)
# elif ambiance_mode == 'reduced':
else:
    print('The thermostat is in reduced mode')
    target_temp = reduced_temp
    set_current_ambiance_mode('reduced')
    if current_temp > target_temp:
        circulator("off")
    else:
        circulator("on")
        regulation(current_temp, target_temp)
