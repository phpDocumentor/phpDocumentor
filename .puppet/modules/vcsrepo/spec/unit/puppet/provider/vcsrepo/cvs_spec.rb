require 'spec_helper'

describe_provider :vcsrepo, :cvs, :resource => {:path => '/tmp/vcsrepo'} do

  describe 'creating' do
    context "with a source", :resource => {:source => ':ext:source@example.com:/foo/bar'} do
      resource_with :revision do
        it "should execute 'cvs checkout' and 'cvs update -r'" do
          provider.expects(:cvs).with('-d', resource.value(:source), 'checkout', '-r', 'an-unimportant-value', '-d', 'vcsrepo', 'bar')
          expects_chdir(File.dirname(resource.value(:path)))
          #provider.expects(:cvs).with('update', '-r', resource.value(:revision), '.')
          provider.create
        end
      end

      resource_without :revision do
        it "should just execute 'cvs checkout' without a revision" do
          provider.expects(:cvs).with('-d', resource.value(:source), 'checkout', '-d', File.basename(resource.value(:path)), File.basename(resource.value(:source)))
          provider.create
        end
      end

      context "with a compression", :resource => {:compression => '3'} do
        it "should just execute 'cvs checkout' without a revision" do
          provider.expects(:cvs).with('-d', resource.value(:source), '-z', '3', 'checkout', '-d', File.basename(resource.value(:path)), File.basename(resource.value(:source)))
          provider.create
        end
      end
    end

    context "when a source is not given" do
      it "should execute 'cvs init'" do
        provider.expects(:cvs).with('-d', resource.value(:path), 'init')
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
    resource_with :source do
      it "should check for the CVS directory" do
        File.expects(:directory?).with(File.join(resource.value(:path), 'CVS'))
        provider.exists?
      end
    end

    resource_without :source do
      it "should check for the CVSROOT directory" do
        File.expects(:directory?).with(File.join(resource.value(:path), 'CVSROOT'))
        provider.exists?
      end
    end
  end

  describe "checking the revision property" do
    before do
      @tag_file = File.join(resource.value(:path), 'CVS', 'Tag')
    end

    context "when CVS/Tag exists" do
      before do
        @tag = 'TAG'
        File.expects(:exist?).with(@tag_file).returns(true)
      end
      it "should read CVS/Tag" do
        File.expects(:read).with(@tag_file).returns("T#{@tag}")
        provider.revision.should == @tag
      end
    end

    context "when CVS/Tag does not exist" do
      before do
        File.expects(:exist?).with(@tag_file).returns(false)
      end
      it "assumes HEAD" do
        provider.revision.should == 'HEAD'
      end
    end
  end

  describe "when setting the revision property" do
    before do
      @tag = 'SOMETAG'
    end

    it "should use 'cvs update -dr'" do
      expects_chdir
      provider.expects(:cvs).with('update', '-dr', @tag, '.')
      provider.revision = @tag
    end
  end

end
