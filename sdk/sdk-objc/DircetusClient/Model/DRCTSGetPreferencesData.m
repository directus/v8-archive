#import "DRCTSGetPreferencesData.h"

@implementation DRCTSGetPreferencesData

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
  return [[JSONKeyMapper alloc] initWithModelToJSONDictionary:@{ @"_id": @"id", @"user": @"user", @"tableName": @"table_name", @"title": @"title", @"columnsVisible": @"columns_visible", @"sort": @"sort", @"sortOrder": @"sort_order", @"status": @"status", @"searchString": @"search_string" }];
}

/**
 * Indicates whether the property with the given name is optional.
 * If `propertyName` is optional, then return `YES`, otherwise return `NO`.
 * This method is used by `JSONModel`.
 */
+ (BOOL)propertyIsOptional:(NSString *)propertyName {

  NSArray *optionalProperties = @[@"_id", @"user", @"tableName", @"title", @"columnsVisible", @"sort", @"sortOrder", @"status", @"searchString"];
  return [optionalProperties containsObject:propertyName];
}

@end
