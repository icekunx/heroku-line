#include <ESP8266TimeAlarms.h>
#include <ESP8266WiFi.h>
const int output5 = 5;

#ifndef WIFI_CONFIG_H
#define YOUR_WIFI_SSID "...."
#define YOUR_WIFI_PASSWD "..."
#endif // !WIFI_CONFIG_H

AlarmId id;

void setup() {
  pinMode(output5, OUTPUT);
  digitalWrite(output5, LOW);
  Serial.begin(115200);
  Serial.println();
  WiFi.mode(WIFI_STA);
  WiFi.begin(" ID ", " Password ");

  configTime(0, 0, "0.se.pool.ntp.org");
  //Europe/Stockholm": "CET-1CEST,M3.5.0,M10.5.0/3"
  //Get JSON of Olson to TZ string using this code https://github.com/pgurenko/tzinfo
  setenv("TZ", "UTC-3", 1);
  tzset();
  Serial.print("Clock before sync: ");
  digitalClockDisplay();
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.print("Clock after Wifi: ");

  // create the alarms, to trigger at specific times
  Alarm.alarmRepeat(10,19,0, MorningAlarm);  // 10:19 am every day
  Alarm.alarmRepeat(10,20,0,EveningAlarm);  // 10:20 am every day
  Alarm.alarmRepeat(dowSaturday,8,30,30,WeeklyAlarm);  // 8:30:30 every Saturday

  // create timers, to trigger relative to when they're created
  Alarm.timerRepeat(15, Repeats);           // timer for every 15 seconds
  id = Alarm.timerRepeat(2, Repeats2);      // timer for every 2 seconds
  Alarm.timerOnce(10, OnceOnly);            // called once after 10 seconds
}

void loop() {
  digitalClockDisplay();
  Alarm.delay(1000); // wait one second between clock display
}

// functions to be called when an alarm triggers:
void MorningAlarm() {
  Serial.println("Alarm: - turn lights ON");
  digitalWrite(output5, HIGH);
}

void EveningAlarm() {
  Serial.println("Alarm: - turn lights OFF");
  digitalWrite(output5, LOW);
}

void WeeklyAlarm() {
  Serial.println("Alarm: - its Monday Morning");
}

void ExplicitAlarm() {
  Serial.println("Alarm: - this triggers only at the given date and time");
}

void Repeats() {
  Serial.println("15 second timer");
}

void Repeats2() {
  Serial.println("2 second timer");
}

void OnceOnly() {
  Serial.println("This timer only triggers once, stop the 2 second timer");
  // use Alarm.free() to disable a timer and recycle its memory.
  Alarm.free(id);
  // optional, but safest to "forget" the ID after memory recycled
  id = dtINVALID_ALARM_ID;
  // you can also use Alarm.disable() to turn the timer off, but keep
  // it in memory, to turn back on later with Alarm.enable().
}

void digitalClockDisplay() {
  time_t tnow = time(nullptr);
  Serial.println(ctime(&tnow));

}
