This Magento module implements the missing "Remember Me" functionality.

Login information and unique secure tokens are stored in a cookie. 
If the user visits the site, the login information from the cookie is compared to the information stored on the server. 
If the tokens do match, the user is logged in. One user can have login cookies on several computers/browsers.
 
> - When the user successfully logs in with Remember Me checked, a login cookie is issued in addition to the standard session management cookie. [1]
> - The login cookie contains the user's username, a series identifier, and a token. The series and token are unguessable random numbers from a suitably large space. All three are stored together in a database table.
> - When a non-logged-in user visits the site and presents a login cookie, the username, series, and token are looked up in the database.
> - If the triplet is present, the user is considered authenticated. The used token is removed from the database. A new token is generated, stored in database with the username and the same series identifier, and a new login cookie containing all three is issued to the user.
> - If the username and series are present but the token does not match, a theft is assumed. The user receives a strongly worded warning and all of the user's remembered sessions are deleted.
> - If the username and series are not present, the login cookie is ignored.

[source][1]

This extension is heavily inspired by:
- Miller Charles's article "[Persistent Login Cookie Best Practice][1]"
- Barry Jaspan's article "[Improved Persistent Login Cookie Best Practice][2]"
- Gabriel Birke's  "[rememberme library][3]"


[1]: http://fishbowl.pastiche.org/2004/01/19/persistent_login_cookie_best_practice.
[2]: http://jaspan.com/improved%5Fpersistent%5Flogin%5Fcookie%5Fbest%5Fpractice
[3]: https://github.com/gbirke/rememberme
