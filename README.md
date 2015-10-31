# UserCakeContactUs
Simple Contact Us Script for UserCake 2.0.2

The Contact Us Script allows visitors and members to submit contact request.  
Administrators can also view all the submitted content.  

Visitors can supply the following information:
-Title
-Message

MySQL Database can be created with the following:

```html
--
-- Table structure for table `uc_contact`
--

CREATE TABLE IF NOT EXISTS `uc_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `con_title` varchar(120) NOT NULL,
  `con_content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
````

Note: Make sure to update the database prefix if it differs from uc_
