# visitors
Creating visits when banner open (test case)

Please find below a task for evaluation of your proficiency level:
The project should consist of 3 files:
- index1.html
- index2.html
- banner.php
and 1 MySQL table with the next mandatory columns:
- ip_address
- user_agent
- view_date
- page_url
- views_count
index1.html and index2.html should have an image tag that inserts some image into the
page using banner.php file: <img src="banner.php">
Every time the image is loaded, the page visitor's info should be recorded in the
MySQL table:
- IP address of the visitor (ip_address column)
- Their user-agent (user_agent column)
- The date and time the image was shown for this visitor (view_date column)
- URL of the page where the image was loaded (page_url column)
- Number of image loads for the same visitor (views_count column) - conditions are
described below.
If a user with the same IP address, user-agent, and page URL hits the page again, the
view_date column has to be updated with the current date and time, as well as
views_count column has to be increased by 1.
The result you need to deliver:
- 4 files total (2 HTML files, 1 PHP file) and a dump file (.sql) of the
table (CREATE+INSERT).
- Image files (optional)
It is highly preferable to implement the code using OOP.
Please let us know how much time did it take you to complete this task
