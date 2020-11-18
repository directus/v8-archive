# IO.Directus.Api.PreferencesApi

All URIs are relative to your configured base path, as per example.

Method | HTTP request | Description
------------- | ------------- | -------------
[**GetPreferences**](PreferencesApi.md#getpreferences) | **GET** /tables/{tableId}/preferences | Returns table preferences
[**UpdatePreferences**](PreferencesApi.md#updatepreferences) | **PUT** /tables/{tableId}/preferences | Update table preferences


<a name="getpreferences"></a>
# **GetPreferences**
> GetPreferences GetPreferences (string tableId)

Returns table preferences

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetPreferencesExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new PreferencesApi();
            var tableId = tableId_example;  // string | ID of table to return rows from

            try
            {
                // Returns table preferences
                GetPreferences result = apiInstance.GetPreferences(tableId);
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling PreferencesApi.GetPreferences: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 

### Return type

[**GetPreferences**](GetPreferences.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="updatepreferences"></a>
# **UpdatePreferences**
> void UpdatePreferences (string tableId, string id = null, string tableName = null, string columnsVisible = null, int? sort = null, string sortOrder = null, string status = null)

Update table preferences

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class UpdatePreferencesExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new PreferencesApi();
            var tableId = tableId_example;  // string | ID of table to return rows from
            var id = id_example;  // string | Preference's Unique Identification number (optional) 
            var tableName = tableName_example;  // string | Name of table to add (optional) 
            var columnsVisible = columnsVisible_example;  // string | List of visible columns, separated by commas (optional) 
            var sort = 56;  // int? | The sort order of the column used to override the column order in the schema (optional) 
            var sortOrder = sortOrder_example;  // string | Sort Order (ASC=Ascending or DESC=Descending) (optional) 
            var status = status_example;  // string | List of status values. separated by comma (optional) 

            try
            {
                // Update table preferences
                apiInstance.UpdatePreferences(tableId, id, tableName, columnsVisible, sort, sortOrder, status);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling PreferencesApi.UpdatePreferences: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 
 **id** | **string**| Preference&#39;s Unique Identification number | [optional] 
 **tableName** | **string**| Name of table to add | [optional] 
 **columnsVisible** | **string**| List of visible columns, separated by commas | [optional] 
 **sort** | **int?**| The sort order of the column used to override the column order in the schema | [optional] 
 **sortOrder** | **string**| Sort Order (ASC&#x3D;Ascending or DESC&#x3D;Descending) | [optional] 
 **status** | **string**| List of status values. separated by comma | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

