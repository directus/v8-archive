# Errors

## General Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `0000` | `500` | Internal Error |
| `0001` | `404` | Not Found |
| `0002` | `400` | Bad Request |
| `0003` | `401` | Unauthorized |
| `0004` | `400` | Invalid Request |
| `0005` | `404` | Endpoint Not Found |
| `0006` | `405` | Method Not Allowed |
| `0007` | `429` | Too Many Requests |
| `0008` | `404` | API Project Configuration Not Found |
| `0009` | `500` | Failed Generating SQL Query |
| `0010` | `403` | Forbidden |
| `0011` | `500` | Failed to Connect to the Database |
| `0012` | `422` | Unprocessable Entity |
| `0013` | `400` | Invalid or Empty Payload |
| `0014` | `503` | Default Project Not Configured Properly |
| `0015` | `400` | Batch Upload Not Allowed |
| `0016` | `500` | Invalid Filesystem Path |
| `0017` | `422` | Invalid Configuration Path |
| `0018` | `409` | Project Name Already Exists |
| `0018` | `401` | Unauthorized Location Access |
| `0019` | `400` | Installation Invalid database information |
| `0020` | `500` | Missing Storage Configuration |
| `0021` | `503` | API is currently under maintenance. |
| `0022` | `503` | Invalid Cache Adapter |
| `0023` | `503` | Invalid Cache Configuration |
| `0024` | `500` | Unknown Project |


## Authentication Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `0100` | `404` | Invalid Credentials |
| `0101` | `401` | Invalid Token |
| `0102` | `401` | Expired Token |
| `0103` | `401` | Inactive User |
| `0104` | `401` | Invalid Reset Password Token |
| `0105` | `401` | Expired Reset Password Token |
| `0106` | `404` | User Not Found |
| `0107` | `404` | User with Provided Email Not Found |
| `0108` | `401` | User Not Authenticated |
| `0109` | `500` | Invalid Request Token |
| `0110` | `500` | Expired Request Token |
| `0111` | `404` | User Missing 2FA OTP |
| `0112` | `404` | Invalid User OTP |
| `0113` | `401` | 2FA Enforced but Not Activated |
| `0114` | `422` | Auth validation error - Invalid Email / Invalid Password |
| `0115` | `400` | SSO not allowed with 2FA enabled |

## Items Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `0200` | `404` | Collection Not Found |
| `0201` | `401` | Not Allow Direct Access To System Table |
| `0202` | `404` | Field Not Found |
| `0203` | `404` | Item Not Found |
| `0204` | `409` | Duplicate Item |
| `0205` |       | Collection Not Managed by Directus |
| `0206` |       | Field Not Managed by Directus |
| `0207` | `404` | Revision Not Found |
| `0208` |       | Revision Has Invalid Delta |
| `0209` | `400` | Field Invalid - A field that doesn't exist for an action such as filtering and sorting |
| `0210` |       | Can Not Create Comment for Item |
| `0211` |       | Can Not Update Comment for Item |
| `0212` |       | Can Not Delete Comment from Item |
| `0213` | `422` | Field does not allow object or array as value |
| `0214` | `422` | Unknown Filter |
| `0215` | `403` | Unable to access data from a related collection |
| `0216` | `403` | Delete/Disable last admin is forbidden |

## Collections Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `0300` | `403` | Reading Items Denied |
| `0301` | `403` | Creating Items Denied |
| `0302` | `403` | Updating Items Denied |
| `0303` | `403` | Deleting Items Denied |
| `0304` | `403` | Reading Field Denied |
| `0305` | `403` | Updating Field Denied |
| `0306` | `403` | Altering Collection Denied |
| `0307` | `422` | Collection Already Exists |
| `0308` | `422` | Field Already Exists |
| `0309` | `403` | Unable to Find Items Owned by User |

## Schema Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `0400` | `500` | Unknown Error |
| `0401` | `400` | Unknown Data Type |
| `0402` | `422` | Field Type Missing Length |
| `0403` | `422` | Field Type Do Not Support Length |
| `0404` | `422` | Unable To Make Field Required When There Are Items In The Collection With No Value For That Field |

## Mail Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `0500` | `500` | Mailer Transport Not Found |
| `0501` | `500` | Invalid Transport Option |
| `0502` | `500` | Invalid Transport Instance |
| `0503` | `500` | Mail Sending Failed |

## Filesystem Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `0600` | `500` | Unknown Error |
| `0601` | `500` | Uploaded File Exceeds Server's Max Upload Size |
| `0602` | `500` | Uploaded File Exceeds Client's Max Upload Size |
| `0603` | `500` | File Only Partially Uploaded |
| `0604` | `500` | No File Uploaded |
| `0605` |       | --- |
| `0606` | `500` | Missing Temporary Upload Directory |
| `0607` | `500` | Failed to Write File to Disk |
| `0608` | `500` | File Upload Stopped by PHP Extension |

## Utils Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `1000` | `400` | Hasher Not Found |