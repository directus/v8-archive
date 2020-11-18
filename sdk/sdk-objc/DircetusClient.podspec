#
# Be sure to run `pod lib lint DircetusClient.podspec' to ensure this is a
# valid spec and remove all comments before submitting the spec.
#
# Any lines starting with a # are optional, but encouraged
#
# To learn more about a Podspec see http://guides.cocoapods.org/syntax/podspec.html
#

Pod::Spec.new do |s|
    s.name             = "DircetusClient"
    s.version          = "1.1.1"

    s.summary          = "directus.io"
    s.description      = <<-DESC
                         API for directus.io
                         DESC

    s.platform     = :ios, '7.0'
    s.requires_arc = true

    s.frameworks = 'SystemConfiguration', 'CoreData'

    s.homepage     = "https://github.com/swagger-api/swagger-codegen"
    s.license      = "Proprietary"
    s.source       = { :git => "https://github.com/swagger-api/swagger-codegen.git", :tag => "#{s.version}" }
    s.author       = { "Swagger" => "apiteam@swagger.io" }

    s.source_files = 'DircetusClient/**/*.{m,h}'
    s.public_header_files = 'DircetusClient/**/*.h'
    s.resources      = 'DircetusClient/**/*.{xcdatamodeld,xcdatamodel}'

    s.dependency 'AFNetworking', '~> 3'
    s.dependency 'JSONModel', '~> 1.2'
    s.dependency 'ISO8601', '~> 0.6'
end

