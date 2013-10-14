class ProviderExampleGroup < Spec::Example::ExampleGroup

  # Allow access to the current resource
  attr_reader :resource

  # Build up the values for the resource in this scope
  before :each do
    resource_hash = example_group_hierarchy.inject({}) do |memo, klass|
      memo.merge(klass.options[:resource] || {})
    end
    full_hash = resource_hash.merge(:provider => described_class.name)
    @resource = described_class.resource_type.new(full_hash)
  end

  # Build the provider
  subject { described_class.new(@resource) }

  # Allow access to it via +provider+
  alias :provider :subject

  # Generate a context for a provider operating on a resource with:
  #
  # call-seq:
  #
  #   # A parameter/property set (when the value isn't important)
  #   resource_with :source do
  #     # ...
  #   end
  #
  #   # A parameter/property set to a specific value
  #   resource_with :source => 'a-specific-value' do
  #     # ...
  #   end
  #
  # Note: Choose one or the other (mixing will create two separate contexts)
  #
  def self.resource_with(*params, &block)
    params_with_values = params.last.is_a?(Hash) ? params.pop : {}
    build_value_context(params_with_values, &block)
    build_existence_context(*params, &block)
  end

  def self.build_existence_context(*params, &block) #:nodoc:
    unless params.empty?
      text = params.join(', ')
      placeholders = params.inject({}) { |memo, key| memo.merge(key => 'an-unimportant-value') }
      context("and with a #{text}", {:resource => placeholders}, &block)
    end
  end

  def self.build_value_context(params = {}, &block) #:nodoc:
    unless params.empty?
      text = params.map { |k, v| "#{k} => #{v.inspect}" }.join(' and with ')
      context("and with #{text}", {:resource => params}, &block)
    end
  end


  # Generate a context for a provider operating on a resource without
  # a given parameter/property.
  #
  # call-seq:
  #
  #   resource_without :source do
  #     # ...
  #   end
  #
  def self.resource_without(field, &block)
    context("and without a #{field}", &block)
  end

end

Spec::Example::ExampleGroupFactory.register(:provider, ProviderExampleGroup)

# Outside wrapper to lookup a provider and start the spec using ProviderExampleGroup
def describe_provider(type_name, provider_name, options = {}, &block)
  provider_class = Puppet::Type.type(type_name).provider(provider_name)
  describe(provider_class, options.merge(:type => :provider), &block)
end
