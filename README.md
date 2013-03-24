This Magento module implement missing "Remember Me" functionality.

Login information and unique secure tokens are stored in a cookie. If the user visits the site, the login information
from the cookie is compared to information stored on the server. If the tokens
match, the user is logged in. A user can have login cookies on several
computers/browsers.

This extension is heavily inspired by:
- Miller Charles's article "[Persistent Login Cookie Best Practice][1]"
- Barry Jaspan's article "[Improved Persistent Login Cookie Best Practice][2]"
- Gabriel Birke's  "[rememberme library][3]"


[1]: http://fishbowl.pastiche.org/2004/01/19/persistent_login_cookie_best_practice.
[2]: http://jaspan.com/improved%5Fpersistent%5Flogin%5Fcookie%5Fbest%5Fpractice
[3]: https://github.com/gbirke/rememberme
