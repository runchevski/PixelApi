
# PixelApi

**PixelApi** is a simple **API** for creating and storing records of **SOI** or **DOI** action.

## Usage

1. Connect your database in the **`app/database.php`** `file`
2. Include the table from the **`database`** `folder`
3. Start the server with ```$ php -S localhost:[port] -t public```
4. Fill out the **`/request`** `page` `form`
5. View the records in **`/database`** `page`

## Request

```javascript
{
	"pixelData": {
		"pixelType":  "string",
		"userId":  integer,
		"occuredOn":  integer,
		"portalId":  integer
	}
}
```

## Response

| Code   | Message                                           |
|--------|---------------------------------------------------|
| `201`  | `OK (Data saved)`                                 |
| `400`  | `Bad Request (Invalid input / Object invalid)`    |
| `401`  | `Unauthorized (An existing item already exists)`  |
| `403`  | `Forbidden (Access denied)`                       |

>`403 Forbidden (Access denied)` will only be available after **`authentication`** is implemented.
>
## Development

In future development it is considered for an **`authentication`** implementation.