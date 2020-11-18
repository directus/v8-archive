<p align="center">
  <a href="https://directus.io" target="_blank" rel="noopener noreferrer">
    <img src="https://user-images.githubusercontent.com/522079/43096167-3a1b1118-8e86-11e8-9fb2-7b4e3b1368bc.png" width="140" alt="Directus Logo"/>
  </a>
</p>

<h1 align="center">
  Directus Java SDK
</h1>

<h3 align="center">
  <a href="https://directus.io">Website</a> • 
  <a href="https://docs.directus.io">Docs</a> • 
  <a href="https://docs.directus.io/api/reference.html">API Reference</a> • 
  <a href="https://docs.directus.io/app/user-guide.html">User Guide</a> • 
  <a href="https://directus.app">Demo</a> • 
  <a href="https://docs.directus.io/supporting-directus.html">Contribute</a>
</h3>

<p>&nbsp;</p>

> _This codebase is a work-in-progress. The repo is here as a placeholder for anyone interested in contributing to the software development kit. Pull-requests and contributions are welcome!_

<p>&nbsp;</p>

## Requirements

Building the API client library requires [Maven](https://maven.apache.org/) to be installed.

## Installation

To install the API client library to your local Maven repository, simply execute:

```shell
mvn install
```

To deploy it to a remote Maven repository instead, configure the settings of the repository and execute:

```shell
mvn deploy
```

Refer to the [official documentation](https://maven.apache.org/plugins/maven-deploy-plugin/usage.html) for more information.

### Maven users

Add this dependency to your project's POM:

```xml
<dependency>
    <groupId>io.directus</groupId>
    <artifactId>directus-sdk</artifactId>
    <version>1.1.1</version>
    <scope>compile</scope>
</dependency>
```

### Gradle users

Add this dependency to your project's build file:

```groovy
compile "io.directus:directus-sdk:1.1.1"
```

### Others

At first generate the JAR by executing:

    mvn package

Then manually install the following JARs:

* target/directus-sdk-1.1.1.jar
* target/lib/*.jar

## Getting Started

Please follow the [installation](#installation) instruction and execute the following Java code:

```java

import io.directus.client.*;
import io.directus.client.auth.*;
import io.directus.client.model.*;
import io.swagger.client.api.ActivityApi;

import java.io.File;
import java.util.*;

public class ActivityApiExample {

    public static void main(String[] args) {
        ApiClient defaultClient = Configuration.getDefaultApiClient();
        
        // Configure API key authorization: api_key
        ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
        api_key.setApiKey("YOUR API KEY");
        // Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
        //api_key.setApiKeyPrefix("Token");

        defaultClient.setBasePath("https://myinstance.directus.io/api/1.1");

        ActivityApi apiInstance = new ActivityApi();
        try {
            GetActivity result = apiInstance.getActivity();
            System.out.println(result);
        } catch (ApiException e) {
            System.err.println("Exception when calling ActivityApi#getActivity");
            e.printStackTrace();
        }
    }
}

```

## Documentation for API Endpoints

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Class | Method | HTTP request | Description
------------ | ------------- | ------------- | -------------
*ActivityApi* | [**getActivity**](docs/ActivityApi.md#getActivity) | **GET** /activity | Returns activity
*BookmarksApi* | [**addBookmark**](docs/BookmarksApi.md#addBookmark) | **POST** /bookmarks | Create a column in a given table
*BookmarksApi* | [**deleteBookmark**](docs/BookmarksApi.md#deleteBookmark) | **DELETE** /bookmarks/{bookmarkId} | Deletes specific bookmark
*BookmarksApi* | [**getBookmark**](docs/BookmarksApi.md#getBookmark) | **GET** /bookmarks/{bookmarkId} | Returns specific bookmark
*BookmarksApi* | [**getBookmarks**](docs/BookmarksApi.md#getBookmarks) | **GET** /bookmarks | Returns bookmarks
*BookmarksApi* | [**getBookmarksSelf**](docs/BookmarksApi.md#getBookmarksSelf) | **GET** /bookmarks/self | Returns bookmarks of current user
*FilesApi* | [**createFile**](docs/FilesApi.md#createFile) | **POST** /files | Creates file
*FilesApi* | [**getFile**](docs/FilesApi.md#getFile) | **GET** /files/{fileId} | Returns specific file by id
*FilesApi* | [**getFiles**](docs/FilesApi.md#getFiles) | **GET** /files | Returns files
*FilesApi* | [**updateFile**](docs/FilesApi.md#updateFile) | **PUT** /files/{fileId} | Creates file
*GroupsApi* | [**addGroup**](docs/GroupsApi.md#addGroup) | **POST** /groups | Add a new group
*GroupsApi* | [**addPrivilege**](docs/GroupsApi.md#addPrivilege) | **POST** /privileges/{groupId} | Create new table privileges for the specified user group
*GroupsApi* | [**getGroup**](docs/GroupsApi.md#getGroup) | **GET** /groups/{groupId} | Returns specific group
*GroupsApi* | [**getGroups**](docs/GroupsApi.md#getGroups) | **GET** /groups | Returns groups
*GroupsApi* | [**getPrivileges**](docs/GroupsApi.md#getPrivileges) | **GET** /privileges/{groupId} | Returns group privileges
*GroupsApi* | [**getPrivilegesForTable**](docs/GroupsApi.md#getPrivilegesForTable) | **GET** /privileges/{groupId}/{tableNameOrPrivilegeId} | Returns group privileges by tableName
*GroupsApi* | [**updatePrivileges**](docs/GroupsApi.md#updatePrivileges) | **PUT** /privileges/{groupId}/{tableNameOrPrivilegeId} | Update privileges by privilegeId
*MessagesApi* | [**getMessage**](docs/MessagesApi.md#getMessage) | **GET** /messages/{messageId} | Returns specific message
*MessagesApi* | [**getMessages**](docs/MessagesApi.md#getMessages) | **GET** /messages/self | Returns messages
*PreferencesApi* | [**getPreferences**](docs/PreferencesApi.md#getPreferences) | **GET** /tables/{tableId}/preferences | Returns table preferences
*PreferencesApi* | [**updatePreferences**](docs/PreferencesApi.md#updatePreferences) | **PUT** /tables/{tableId}/preferences | Update table preferences
*SettingsApi* | [**getSettings**](docs/SettingsApi.md#getSettings) | **GET** /settings | Returns settings
*SettingsApi* | [**getSettingsFor**](docs/SettingsApi.md#getSettingsFor) | **GET** /settings/{collectionName} | Returns settings for collection
*SettingsApi* | [**updateSettings**](docs/SettingsApi.md#updateSettings) | **PUT** /settings/{collectionName} | Update settings
*TablesApi* | [**addColumn**](docs/TablesApi.md#addColumn) | **POST** /tables/{tableId}/columns | Create a column in a given table
*TablesApi* | [**addRow**](docs/TablesApi.md#addRow) | **POST** /tables/{tableId}/rows | Add a new row
*TablesApi* | [**addTable**](docs/TablesApi.md#addTable) | **POST** /tables | Add a new table
*TablesApi* | [**deleteColumn**](docs/TablesApi.md#deleteColumn) | **DELETE** /tables/{tableId}/columns/{columnName} | Delete row
*TablesApi* | [**deleteRow**](docs/TablesApi.md#deleteRow) | **DELETE** /tables/{tableId}/rows/{rowId} | Delete row
*TablesApi* | [**deleteTable**](docs/TablesApi.md#deleteTable) | **DELETE** /tables/{tableId} | Delete Table
*TablesApi* | [**getTable**](docs/TablesApi.md#getTable) | **GET** /tables/{tableId} | Returns specific table
*TablesApi* | [**getTableColumn**](docs/TablesApi.md#getTableColumn) | **GET** /tables/{tableId}/columns/{columnName} | Returns specific table column
*TablesApi* | [**getTableColumns**](docs/TablesApi.md#getTableColumns) | **GET** /tables/{tableId}/columns | Returns table columns
*TablesApi* | [**getTableRow**](docs/TablesApi.md#getTableRow) | **GET** /tables/{tableId}/rows/{rowId} | Returns specific table row
*TablesApi* | [**getTableRows**](docs/TablesApi.md#getTableRows) | **GET** /tables/{tableId}/rows | Returns table rows
*TablesApi* | [**getTables**](docs/TablesApi.md#getTables) | **GET** /tables | Returns tables
*TablesApi* | [**updateColumn**](docs/TablesApi.md#updateColumn) | **PUT** /tables/{tableId}/columns/{columnName} | Update column
*TablesApi* | [**updateRow**](docs/TablesApi.md#updateRow) | **PUT** /tables/{tableId}/rows/{rowId} | Update row
*UtilsApi* | [**getHash**](docs/UtilsApi.md#getHash) | **POST** /hash | Get a hashed value
*UtilsApi* | [**getRandom**](docs/UtilsApi.md#getRandom) | **POST** /random | Returns random alphanumeric string


## Documentation for Models

 - [GetActivity](docs/GetActivity.md)
 - [GetActivityData](docs/GetActivityData.md)
 - [GetActivityMeta](docs/GetActivityMeta.md)
 - [GetBookmark](docs/GetBookmark.md)
 - [GetBookmarks](docs/GetBookmarks.md)
 - [GetBookmarksData](docs/GetBookmarksData.md)
 - [GetBookmarksMeta](docs/GetBookmarksMeta.md)
 - [GetFile](docs/GetFile.md)
 - [GetFiles](docs/GetFiles.md)
 - [GetFilesData](docs/GetFilesData.md)
 - [GetGroup](docs/GetGroup.md)
 - [GetGroups](docs/GetGroups.md)
 - [GetGroupsData](docs/GetGroupsData.md)
 - [GetGroupsDataData](docs/GetGroupsDataData.md)
 - [GetGroupsDataMeta](docs/GetGroupsDataMeta.md)
 - [GetMessage](docs/GetMessage.md)
 - [GetMessages](docs/GetMessages.md)
 - [GetMessagesData](docs/GetMessagesData.md)
 - [GetMessagesMeta](docs/GetMessagesMeta.md)
 - [GetMessagesResponses](docs/GetMessagesResponses.md)
 - [GetPreferences](docs/GetPreferences.md)
 - [GetPreferencesData](docs/GetPreferencesData.md)
 - [GetPrivileges](docs/GetPrivileges.md)
 - [GetPrivilegesData](docs/GetPrivilegesData.md)
 - [GetPrivilegesForTable](docs/GetPrivilegesForTable.md)
 - [GetSettings](docs/GetSettings.md)
 - [GetSettingsData](docs/GetSettingsData.md)
 - [GetSettingsDataFiles](docs/GetSettingsDataFiles.md)
 - [GetSettingsDataGlobal](docs/GetSettingsDataGlobal.md)
 - [GetSettingsFor](docs/GetSettingsFor.md)
 - [GetSettingsForMeta](docs/GetSettingsForMeta.md)
 - [GetTable](docs/GetTable.md)
 - [GetTableColumn](docs/GetTableColumn.md)
 - [GetTableColumnData](docs/GetTableColumnData.md)
 - [GetTableColumns](docs/GetTableColumns.md)
 - [GetTableColumnsData](docs/GetTableColumnsData.md)
 - [GetTableData](docs/GetTableData.md)
 - [GetTableRow](docs/GetTableRow.md)
 - [GetTableRows](docs/GetTableRows.md)
 - [GetTableRowsData](docs/GetTableRowsData.md)
 - [GetTableRowsMeta](docs/GetTableRowsMeta.md)
 - [GetTables](docs/GetTables.md)
 - [GetTablesData](docs/GetTablesData.md)
 - [GetTablesMeta](docs/GetTablesMeta.md)


## Documentation for Authorization

Authentication schemes defined for the API:
### api_key

- **Type**: API key
- **API key parameter name**: access_token
- **Location**: URL query string


## Recommendation

It's recommended to create an instance of `ApiClient` per thread in a multithreaded environment to avoid any potential issues.

<p>&nbsp;</p>

----

<p align="center">
  Directus is released under the <a href="http://www.gnu.org/copyleft/gpl.html">GPLv3</a> license. <a href="http://rangerstudio.com">RANGER Studio LLC</a> owns all Directus trademarks and logos on behalf of our project's community. Copyright © 2006-2018, <a href="http://rangerstudio.com">RANGER Studio LLC</a>.
</p>

