#import "DRCTSGetSettingsDataFiles.h"

@implementation DRCTSGetSettingsDataFiles

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
  return [[JSONKeyMapper alloc] initWithModelToJSONDictionary:@{ @"allowedThumbnails": @"allowed_thumbnails", @"thumbnailQuality": @"thumbnail_quality", @"thumbnailSize": @"thumbnail_size", @"fileNaming": @"file_naming", @"thumbnailCropEnabled": @"thumbnail_crop_enabled", @"youtubeApiKey": @"youtube_api_key" }];
}

/**
 * Indicates whether the property with the given name is optional.
 * If `propertyName` is optional, then return `YES`, otherwise return `NO`.
 * This method is used by `JSONModel`.
 */
+ (BOOL)propertyIsOptional:(NSString *)propertyName {

  NSArray *optionalProperties = @[@"allowedThumbnails", @"thumbnailQuality", @"thumbnailSize", @"fileNaming", @"thumbnailCropEnabled", @"youtubeApiKey"];
  return [optionalProperties containsObject:propertyName];
}

@end
