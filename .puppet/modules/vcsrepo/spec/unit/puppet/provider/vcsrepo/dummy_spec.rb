require 'spec_helper'

describe_provider :vcsrepo, :dummy, :resource => {:path => '/tmp/vcsrepo'} do

  context 'dummy' do
    resource_with :source do
      resource_with :ensure => :present do
        context "with nothing doing", :resource => {:revision => 'foo'} do
          it "should raise an exception" do
            proc { provider.working_copy_exists? }.should raise_error(RuntimeError)
          end
        end
      end
    end
  end

end
