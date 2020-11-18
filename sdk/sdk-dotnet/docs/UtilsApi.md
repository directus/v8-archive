# IO.Directus.Api.UtilsApi

All URIs are relative to your configured base path, as per example.

Method | HTTP request | Description
------------- | ------------- | -------------
[**GetHash**](UtilsApi.md#gethash) | **POST** /hash | Get a hashed value
[**GetRandom**](UtilsApi.md#getrandom) | **POST** /random | Returns random alphanumeric string


<a name="gethash"></a>
# **GetHash**
> void GetHash (string _string = null, string hasher = null, string options = null)

Get a hashed value

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetHashExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new UtilsApi();
            var _string = _string_example;  // string | The string to be hashed (optional) 
            var hasher = hasher_example;  // string | The hasher used to hash the given string (optional)  (default to core)
            var options = options_example;  // string | The hasher options (optional) 

            try
            {
                // Get a hashed value
                apiInstance.GetHash(_string, hasher, options);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling UtilsApi.GetHash: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **_string** | **string**| The string to be hashed | [optional] 
 **hasher** | **string**| The hasher used to hash the given string | [optional] [default to core]
 **options** | **string**| The hasher options | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="getrandom"></a>
# **GetRandom**
> void GetRandom (string length = null)

Returns random alphanumeric string

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetRandomExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new UtilsApi();
            var length = length_example;  // string | Integer(String) for length of random string (optional) 

            try
            {
                // Returns random alphanumeric string
                apiInstance.GetRandom(length);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling UtilsApi.GetRandom: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **length** | **string**| Integer(String) for length of random string | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

