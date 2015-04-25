RaceTimingSubGenerator
======================

Sample result video: https://www.youtube.com/watch?v=pZfw44pyEMY

Cleaned up OOP version of norcal-timing-etc project, with interface and easier usability function added.

Most information can be found in an earlier project called: norcal-hobbies-result-subtitle-generator.

It's providing basically the same function (generates same subtitle file as output).

Changes:

1. Overhauled the code, so it's easier to maintain. (old project started as an idea with 3 evenings' hack).

2. Added an not so good look, but functional interface. 

3. Accepts 3 types of input files:

  -MyLaps data from official website.
  
  -RCScoringPro software generated detailed output text sheet.
  
  -Go Kart Racer result sheet from their website.
  
4. Added function to make the sync job easier. Before, it requires calculation and trial to sync subtitle with video.

  Now, you just need to find any driver's any cross line time in video, input into the interface and it will calcuate the sync timing for you.
  
