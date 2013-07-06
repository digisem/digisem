digisem
=======

A moodle plug-in that allows to request and automatically publish documents in courses. 

Objective
=======
This project was initiated at the TU München 2011 and serves as interface between the library services and the central learning management system moodle. 
The goals are:
<ul>
<li>Requesting digital copies of books or articles from the library for moodle courses</li>
<li>Automatic delivery of these digital copies to the moodle and integration as course material in the course</li>
</ul>

How does it work? 
<ol>
<li>Course manager Alice wants to provide chapter 1 from "Wealth of nations" of Adam Smith for her introductionary course on economics for her students. She would like to save a digital copy of the chapter in the respective moodle course room. </li>
<li>She navigates to the course room in moodle and creates a "digisem" and is redirected to the library online catalogue. </li>
<li>Alice finds the book in the library online catalogue and uses the link back to moodle. </li>
<li>The meta data is imported into moodle automatically and Alice can provide the chapter and pages in the "digisem" form. </li>
<li>She submits the form. </li>
</ol>

In the background the digisem plugin sends a request mail to the library. They scan the chapter and publish the copy on a FTP server. The digisem plugin scans the server continuously, finds the document and integrates it into the moodle course room. 

There seems to be quite some magic in some of the described steps. So why not look at it a bit more closely:
Step 3: The digisem plugin has a HTTP-Get-Interface with parameters you can freely fill. The library sends the meta info of the book or article by using this interface back to the moodle plugin digisem. 
Background: digisem uses the SUBITO format to send the request mail. This format is the standard for requesting online copies of library documents. 
