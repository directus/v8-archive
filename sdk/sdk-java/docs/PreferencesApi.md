# PreferencesApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getPreferences**](PreferencesApi.md#getPreferences) | **GET** /tables/{tableId}/preferences | Returns table preferences
[**updatePreferences**](PreferencesApi.md#updatePreferences) | **PUT** /tables/{tableId}/preferences | Update table preferences


<a name="getPreferences"></a>
# **getPreferences**
> GetPreferences getPreferences(tableId)

Returns table preferences

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.PreferencesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

PreferencesApi apiInstance = new PreferencesApi();
String tableId = "tableId_example"; // String | ID of table to return rows from
try {
    GetPreferences result = apiInstance.getPreferences(tableId);
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling PreferencesApi#getPreferences");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **String**| ID of table to return rows from |

### Return type

[**GetPreferences**](GetPreferences.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="updatePreferences"></a>
# **updatePreferences**
> updatePreferences(tableId, id, tableName, columnsVisible, sort, sortOrder, status)

Update table preferences

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.PreferencesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

PreferencesApi apiInstance = new PreferencesApi();
String tableId = "tableId_example"; // String | ID of table to return rows from
String id = "id_example"; // String | Preference's Unique Identification number
String tableName = "tableName_example"; // String | Name of table to add
String columnsVisible = "columnsVisible_example"; // String | List of visible columns, separated by commas
Integer sort = 56; // Integer | The sort order of the column used to override the column order in the schema
String sortOrder = "sortOrder_example"; // String | Sort Order (ASC=Ascending or DESC=Descending)
String status = "status_example"; // String | List of status values. separated by comma
try {
    apiInstance.updatePreferences(tableId, id, tableName, columnsVisible, sort, sortOrder, status);
} catch (ApiException e) {
    System.err.println("Exception when calling PreferencesApi#updatePreferences");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **String**| ID of table to return rows from |
 **id** | **String**| Preference&#39;s Unique Identification number | [optional]
 **tableName** | **String**| Name of table to add | [optional]
 **columnsVisible** | **String**| List of visible columns, separated by commas | [optional]
 **sort** | **Integer**| The sort order of the column used to override the column order in the schema | [optional]
 **sortOrder** | **String**| Sort Order (ASC&#x3D;Ascending or DESC&#x3D;Descending) | [optional] [enum: ASC, DESC]
 **status** | **String**| List of status values. separated by comma | [optional]

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

