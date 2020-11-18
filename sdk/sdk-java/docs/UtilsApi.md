# UtilsApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getHash**](UtilsApi.md#getHash) | **POST** /hash | Get a hashed value
[**getRandom**](UtilsApi.md#getRandom) | **POST** /random | Returns random alphanumeric string


<a name="getHash"></a>
# **getHash**
> getHash(string, hasher, options)

Get a hashed value

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.UtilsApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

UtilsApi apiInstance = new UtilsApi();
String string = "string_example"; // String | The string to be hashed
String hasher = "core"; // String | The hasher used to hash the given string
String options = "options_example"; // String | The hasher options
try {
    apiInstance.getHash(string, hasher, options);
} catch (ApiException e) {
    System.err.println("Exception when calling UtilsApi#getHash");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **string** | **String**| The string to be hashed | [optional]
 **hasher** | **String**| The hasher used to hash the given string | [optional] [default to core] [enum: core, bcrypt, sha1, sha224, sha256, sha384, sha512]
 **options** | **String**| The hasher options | [optional]

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

<a name="getRandom"></a>
# **getRandom**
> getRandom(length)

Returns random alphanumeric string

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.UtilsApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

UtilsApi apiInstance = new UtilsApi();
String length = "length_example"; // String | Integer(String) for length of random string
try {
    apiInstance.getRandom(length);
} catch (ApiException e) {
    System.err.println("Exception when calling UtilsApi#getRandom");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **length** | **String**| Integer(String) for length of random string | [optional]

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

