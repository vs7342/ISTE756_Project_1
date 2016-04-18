# The Chocolate Factory *v1.0* #
-------------------------------------------------------------------
An E-Commerce website developed as a part of ISTE-756-Server Design and Development course.

### Technical Overview ###

A data-driven website built using PHP and MySQL. External CSS along with Bootstrap was used extensively. PHP Session was used to manage different user functionality / access. Database has three tables: Cart, Products and User. Home page is rendered as per the data in Products table. Cart page is rendered based on the user and the corresponding products present in the Cart table.

### Technologies Used ###

- PHP
- HTML
- CSS
- Bootstrap
- MySQL

### Project Overview ###
A simple E-Commerce website in which customer can add items to their cart and clear the cart on a single button click. Cart details are maintained even if the user logs out or the session ends. Admin can add/update product details.

### Project Description ###
- The Home page has 2 sections - Sales and Catalog. Sales section lists the items which are on sale and Catalog section lists the items not on sale. A member can add a particular item to the cart by clicking 'Add to Cart' button. Available Quantity is also displayed for every single item which is updated instantly when a user adds a product to the cart.
- Cart page lists the items present in the current user's cart. User can empty the cart using  If the user is not logged in, empty cart will be displayed. 
- Admin page has 2 sections for adding a product or updating an existing one.
- At any given time, there is maximum of 5 items and minimum of 3 items on sale.
- Only 5 items are displayed in the catalog section on the home page. Paging is handled on the server-side.
- User has to log in before adding products to cart.
- Admin link is displayed only when an Admin user logs in.
- Image needs to be uploaded for every new product entry. Image must be in png format and its size should be less than 1MB.

### Types of User ###
PHP Session was used to manage different user functionality / access.

- General User
	- Can view the catalog and sales section
	- Can Sign-Up to become a member 
	- Cannot add items to the cart unless logged in as a member
- Member
	- Can add item to the cart from sales/catalog section
	- Can empty the entire cart while on the cart page
- Admin
	- Can add or update the products
	- Member functionality also given to Admin

### File Structure/Description ###

- **Classes folder**:

	*Data Layer*
	- DB.class.php - Interacts with the database and does the basic CRUD operations. Includes getData() and setData() methods. PDO along with paramaterized queries have been used.
	- DLException.class.php - Data Layer Exception class which throws Exceptions encountered in DB.class.php (PDOException) and logs the relevant details in log.txt file which is generated in the root folder.
	- Product.class.php - Interacts with DB.class.php. Includes post(), put(), and fetch() methods to insert, update, delete and fetch(select) data from products table.
	- User.class.php and Cart.class.php are similar to Product.class.php but operating with User and Cart tables respectively.

	*Business Layer*

	BLProduct.class.php, BLUser.class.php, BLCart.class.php consists of business layer logic. Currently, BLProduct.class.php is the only business layer file which has some code in it which handles the sales constraint (Maximum of 5 items and Minimum of 3 items on sale).

- **Images folder**: Whenever a product image is uploaded, a random name is given to that file and is saved in this folder. The random image name is stored in the database against that particular item. Old image is deleted when a product is updated with a new image.

- **Root folder**: Consists of all the pages user interacts with. LIB_project1.php contains of all the utility function and is included by almost all the scripts in this folder. log.txt is used to log all the details of Data Layer Exception encountered.

### Future work ###
- A member can add a user-defined quantity of product(s) to the cart.
- Admin can delete a product from the database.
- Admin can add/update/delete a specific user from database
- Member can delete a specific product from the cart rather than having a single button to empty the entire cart.

