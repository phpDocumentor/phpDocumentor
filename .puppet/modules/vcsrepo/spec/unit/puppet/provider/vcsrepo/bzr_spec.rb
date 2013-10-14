require 'spec_helper'

describe_provider :vcsrepo, :bzr, :resource => {:path => '/tmp/vcsrepo'} do

  describe 'creating' do
    resource_with :source do
      resource_with :revision do
        it "should execute 'bzr clone -r' with the revision" do
          provider.expects(:bzr).with('branch', '-r', resource.value(:revision), resource.value(:source), resource.value(:path))
          provider.create
        end
      end
      resource_without :revision do
        it "should just execute 'bzr clone' without a revision" do
          provider.expects(:bzr).with('branch', resource.value(:source), resource.value(:path))
          provider.create
        end
      end
    end
    resource_without :source do
      it "should execute 'bzr init'" do
        provider.expects(:bzr).with('init', resource.value(:path))
        provider.create
      end
    end
  end

  describe 'destroying' do
    it "it should remove the directory" do
      expects_rm_rf
      provider.destroy
    end
  end

  describe "checking existence" do
    it "should check for the directory" do
      expects_directory?(true, File.join(resource.value(:path), '.bzr'))
      provider.exists?
    end
  end

  describe "checking the revision property" do
    before do
      expects_chdir
      provider.expects(:bzr).with('version-info').returns(fixture(:bzr_version_info))
      @current_revid = 'menesis@pov.lt-20100309191856-4wmfqzc803fj300x'
    end
    context "when given a non-revid as the resource revision", :resource => {:revision => '2634'} do
      context "when its revid is not different than the current revid" do
        before do
          provider.expects(:bzr).with('revision-info', resource.value(:revision)).returns("#{resource.value(:revision)} menesis@pov.lt-20100309191856-4wmfqzc803fj300x\n")
        end
        it "should return the ref" do
          provider.revision.should == resource.value(:revision)
        end
      end
      context "when its revid is different than the current revid", :resource => {:revision => '2636'} do
        it "should return the current revid" do
          provider.expects(:bzr).with('revision-info', resource.value(:revision)).returns("2635 foo\n")
          provider.revision.should == @current_revid
        end
      end
    end
    context "when given a revid as the resource revision" do
      context "when it is the same as the current revid", :resource => {:revision => 'menesis@pov.lt-20100309191856-4wmfqzc803fj300x'} do
        before do
          provider.expects(:bzr).with('revision-info', resource.value(:revision)).returns("1234 #{resource.value(:revision)}\n")
        end
        it "should return it" do
          provider.revision.should == resource.value(:revision)
        end
      end
      context "when it is not the same as the current revid", :resource => {:revision => 'menesis@pov.lt-20100309191856-4wmfqzc803fj300y'} do
        it "should return the current revid" do
          provider.expects(:bzr).with('revision-info', resource.value(:revision)).returns("2636 foo\n")
          provider.revision.should == @current_revid
        end
      end
    end
  end

  describe "setting the revision property" do
    it "should use 'bzr update -r' with the revision" do
      revision = 'somerev'
      provider.expects(:bzr).with('update', '-r', revision, resource.value(:path))
      provider.revision = revision
    end
  end

end
