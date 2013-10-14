require 'spec_helper'

describe_provider :vcsrepo, :git, :resource => {:path => '/tmp/vcsrepo'} do

  context 'creating' do
    resource_with :source do
      resource_with :ensure => :present do
        context "with a revision that is a remote branch", :resource => {:revision => 'only/remote'} do
          it "should execute 'git clone' and 'git checkout -b'" do
            provider.expects(:git).with('clone', resource.value(:source), resource.value(:path))
            expects_chdir
            provider.expects(:update_submodules)
            provider.expects(:git).with('branch', '-a').returns(resource.value(:revision))
            provider.expects(:git).with('checkout', '--force', resource.value(:revision))
            provider.create
          end
        end
        context "with a revision that is not a remote branch", :resource => {:revision => 'a-commit-or-tag'} do
          it "should execute 'git clone' and 'git reset --hard'" do
            provider.expects(:git).with('clone', resource.value(:source), resource.value(:path))
            expects_chdir
            provider.expects(:update_submodules)
            provider.expects(:git).with('branch', '-a').returns(resource.value(:revision))
            provider.expects(:git).with('checkout', '--force', resource.value(:revision))
            provider.create
          end
        end
        resource_without :revision do
          it "should execute 'git clone' and submodule commands" do
            provider.expects(:git).with('clone', resource.value(:source), resource.value(:path))
            provider.expects(:update_submodules)
            provider.create
          end
        end
      end

      resource_with :ensure => :bare do
        resource_with :revision do
          it "should just execute 'git clone --bare'" do
            provider.expects(:git).with('clone', '--bare', resource.value(:source), resource.value(:path))
            provider.create
          end
        end

        resource_without :revision do
          it "should just execute 'git clone --bare'" do
            provider.expects(:git).with('clone', '--bare', resource.value(:source), resource.value(:path))
            provider.create
          end
        end
      end
    end

    context "when a source is not given" do
      resource_with :ensure => :present do
        context "when the path does not exist" do
          it "should execute 'git init'" do
            expects_mkdir
            expects_chdir
            expects_directory?(false)
            provider.expects(:bare_exists?).returns(false)
            provider.expects(:git).with('init')
            provider.create
          end
        end

        context "when the path is a bare repository" do
          it "should convert it to a working copy" do
            provider.expects(:bare_exists?).returns(true)
            provider.expects(:convert_bare_to_working_copy)
            provider.create
          end
        end

        context "when the path is not a repository" do
          it "should raise an exception" do
            provider.expects(:path_exists?).returns(true)
            proc { provider.create }.should raise_error(Puppet::Error)
          end
        end
      end

      resource_with :ensure => :bare do
        context "when the path does not exist" do
          it "should execute 'git init --bare'" do
            expects_chdir
            expects_mkdir
            expects_directory?(false)
            provider.expects(:working_copy_exists?).returns(false)
            provider.expects(:git).with('init', '--bare')
            provider.create
          end
        end

        context "when the path is a working copy repository" do
          it "should convert it to a bare repository" do
            provider.expects(:working_copy_exists?).returns(true)
            provider.expects(:convert_working_copy_to_bare)
            provider.create
          end
        end

        context "when the path is not a repository" do
          it "should raise an exception" do
            expects_directory?(true)
            proc { provider.create }.should raise_error(Puppet::Error)
          end
        end
      end
    end

  end

  context 'destroying' do
    it "it should remove the directory" do
      expects_rm_rf
      provider.destroy
    end
  end

  context "checking the revision property" do
    resource_with :revision do
      before do
        expects_chdir
        provider.expects(:git).with('rev-parse', 'HEAD').returns('currentsha')
      end

      context "when its SHA is not different than the current SHA" do
        it "should return the ref" do
          provider.expects(:git).with('config', 'remote.origin.url').returns('')
          provider.expects(:git).with('fetch', 'origin') # FIXME
          provider.expects(:git).with('fetch', '--tags', 'origin')
          provider.expects(:git).with('rev-parse', resource.value(:revision)).returns('currentsha')
          provider.expects(:git).with('tag', '-l').returns("Hello")
          provider.revision.should == resource.value(:revision)
        end
      end

      context "when its SHA is different than the current SHA" do
        it "should return the current SHA" do
          provider.expects(:git).with('config', 'remote.origin.url').returns('')
          provider.expects(:git).with('fetch', 'origin') # FIXME
          provider.expects(:git).with('fetch', '--tags', 'origin')
          provider.expects(:git).with('rev-parse', resource.value(:revision)).returns('othersha')
          provider.expects(:git).with('tag', '-l').returns("Hello")
          provider.revision.should == 'currentsha'
        end
      end

      context "when the source is modified" do
        resource_with :source => 'git://git@foo.com/bar.git' do
          it "should update the origin url" do
            provider.expects(:git).with('config', 'remote.origin.url').returns('old')
            provider.expects(:git).with('config', 'remote.origin.url', 'git://git@foo.com/bar.git')
            provider.expects(:git).with('fetch', 'origin') # FIXME
            provider.expects(:git).with('fetch', '--tags', 'origin')
            provider.expects(:git).with('rev-parse', resource.value(:revision)).returns('currentsha')
            provider.expects(:git).with('tag', '-l').returns("Hello")
            provider.revision.should == resource.value(:revision)
          end
        end
      end
    end
  end

  context "setting the revision property" do
    before do
      expects_chdir
    end
    context "when it's an existing local branch", :resource => {:revision => 'feature/foo'} do
      it "should use 'git fetch' and 'git reset'" do
        provider.expects(:update_submodules)
        provider.expects(:git).with('branch', '-a').returns(resource.value(:revision))
        provider.expects(:git).with('checkout', '--force', resource.value(:revision))
        provider.expects(:git).with('branch', '-a').returns(resource.value(:revision))
        provider.expects(:git).with('reset', '--hard', "origin/#{resource.value(:revision)}")
        provider.revision = resource.value(:revision)
      end
    end
    context "when it's a remote branch", :resource => {:revision => 'only/remote'} do
      it "should use 'git fetch' and 'git reset'" do
        provider.expects(:update_submodules)
        provider.expects(:git).with('branch', '-a').returns(resource.value(:revision))
        provider.expects(:git).with('checkout', '--force', resource.value(:revision))
        provider.expects(:git).with('branch', '-a').returns(resource.value(:revision))
        provider.expects(:git).with('reset', '--hard', "origin/#{resource.value(:revision)}")
        provider.revision = resource.value(:revision)
      end
    end
    context "when it's a commit or tag", :resource => {:revision => 'a-commit-or-tag'} do
      it "should use 'git fetch' and 'git reset'" do
        provider.expects(:git).with('branch', '-a').returns(fixture(:git_branch_a))
        provider.expects(:git).with('checkout', '--force', resource.value(:revision))
        provider.expects(:git).with('branch', '-a').returns(fixture(:git_branch_a))
        provider.expects(:git).with('submodule', 'init')
        provider.expects(:git).with('submodule', 'update')
        provider.expects(:git).with('branch', '-a').returns(fixture(:git_branch_a))
        provider.expects(:git).with('submodule', 'foreach', 'git', 'submodule', 'init')
        provider.expects(:git).with('submodule', 'foreach', 'git', 'submodule', 'update')
        provider.revision = resource.value(:revision)
      end
    end
  end

  context "updating references" do
    it "should use 'git fetch --tags'" do
      expects_chdir
      provider.expects(:git).with('config', 'remote.origin.url').returns('')
      provider.expects(:git).with('fetch', 'origin')
      provider.expects(:git).with('fetch', '--tags', 'origin')
      provider.update_references
    end
  end

  context "checking if revision" do
    before do
      expects_chdir
      provider.expects(:git).with('branch', '-a').returns(fixture(:git_branch_a))
    end
    context "is a local branch" do
      context "when it's listed in 'git branch -a'", :resource => {:revision => 'feature/foo'} do
        it "should return true" do
          provider.should be_local_branch_revision
        end
      end
      context "when it's not listed in 'git branch -a'" , :resource => {:revision => 'feature/notexist'}do
        it "should return false" do
          provider.should_not be_local_branch_revision
        end
      end
    end
    context "is a remote branch" do
      context "when it's listed in 'git branch -a' with an 'origin/' prefix", :resource => {:revision => 'only/remote'} do
        it "should return true" do
          provider.should be_remote_branch_revision
        end
      end
      context "when it's not listed in 'git branch -a' with an 'origin/' prefix" , :resource => {:revision => 'only/local'}do
        it "should return false" do
          provider.should_not be_remote_branch_revision
        end
      end
    end
  end

end
