#import "DRCTSGetTableData.h"

@implementation DRCTSGetTableData

- (instancetype)init {
  self = [super init];
  if (self) {
    // initialize property's default value, if any
    
  }
  return self;
}


/**
 * Maps json key to property name.
 * This method is used by `JSONModel`.
 */
+ (JSONKeyMapper *)keyMapper {
  return [[JSONKeyMapper alloc] initWithModelToJSONDictionary:@{ @"name": @"name", @"_id": @"id", @"tableName": @"table_name", @"columns": @"columns", @"preferences": @"preferences", @"primaryColumn": @"primary_column", @"schema": @"schema", @"hidden": @"hidden", @"single": @"single", @"defaultStatus": @"default_status", @"userCreateColumn": @"user_create_column", @"userUpdateColumn": @"user_update_column", @"dateCreateColumn": @"date_create_column", @"dateUpdateColumn": @"date_update_column", @"createdAt": @"created_at", @"dateCreated": @"date_created", @"comment": @"comment", @"rowCount": @"row_count", @"footer": @"footer", @"listView": @"list_view", @"columnGroupings": @"column_groupings", @"filterColumnBlacklist": @"filter_column_blacklist" }];
}

/**
 * Indicates whether the property with the given name is optional.
 * If `propertyName` is optional, then return `YES`, otherwise return `NO`.
 * This method is used by `JSONModel`.
 */
+ (BOOL)propertyIsOptional:(NSString *)propertyName {

  NSArray *optionalProperties = @[@"name", @"_id", @"tableName", @"columns", @"preferences", @"primaryColumn", @"schema", @"hidden", @"single", @"defaultStatus", @"userCreateColumn", @"userUpdateColumn", @"dateCreateColumn", @"dateUpdateColumn", @"createdAt", @"dateCreated", @"comment", @"rowCount", @"footer", @"listView", @"columnGroupings", @"filterColumnBlacklist"];
  return [optionalProperties containsObject:propertyName];
}

@end
