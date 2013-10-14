require 'pathname'
dir = Pathname.new(__FILE__).parent
$LOAD_PATH.unshift(dir, dir + 'lib', dir + '../lib')

require 'test/unit'
require 'mocha'
require 'puppet'
gem 'rspec', '>= 1.2.9'
require 'spec/autorun'

Dir[File.join(File.dirname(__FILE__), 'support', '*.rb')].each do |support_file|
  require support_file
end

Spec::Runner.configure do |config|
  config.mock_with :mocha
  config.include(FixtureHelpers)
  config.include(FilesystemHelpers)
end

# We need this because the RAL uses 'should' as a method.  This
# allows us the same behaviour but with a different method name.
class Object
    alias :must :should
end
