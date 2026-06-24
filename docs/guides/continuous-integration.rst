=====================
Continuous Integration
=====================

Just like any other application component your documentation can be build automatically using a continuous
service. This is a great way to ensure that your documentation is always up to date and that
it is build correctly. This chapter will show you how to use phpDocumentor in combination with different
continuous integration services.

GitHub Actions
==============

Here is a working starting point to deploy phpDocs to your GitHub Pages for a project. You may also want
to update this to handle multiple versions of your project instead of just the latest.

.. code-block:: yaml

    name: Generate phpDoc
    
    # Allow GITHUB_TOKEN to deploy to GitHub Pages
    permissions:
      contents: read
      pages: write
      id-token: write
    
    on:
      workflow_dispatch: # Allow to manually deploy
      push:
        branches: ["main"]
    
    jobs:
      build:
        runs-on: ubuntu-latest
        concurrency:
          group: ${{ github.workflow }}-${{ github.ref }}
          cancel-in-progress: true
        steps:
          - name: Checkout source code
            uses: actions/checkout@v4
            with:
              path: source
    
          # Install phpDocumentator using PHIVE
          # phpDocumentator recommends PHIVE as the preferred install strategy
          # Source: https://github.com/phpDocumentor/phpDocumentor/blob/919d5c1ef42a3c74d050e05ce99add6efa87b5a4/README.md?plain=1#L79
          - name: Cache PHIVE tools
            uses: actions/cache@v4
            with:
              path: ${{ runner.temp }}/.phive
              key: php-phive-${{ hashFiles('.phive/phars.xml') }}
              restore-keys: php-phive-
    
          - name: Install PHIVE
            uses: szepeviktor/phive@v1
            with:
              home: ${{ runner.temp }}/.phive
              binPath: ${{ github.workspace }}/tools/phive
    
          # TODO: confirm this is the correct GPG key
          # Blocker: https://github.com/phpDocumentor/phpDocumentor/issues/3694
          - name: Install phpDocumentor
            run: ${{ github.workspace }}/tools/phive install phpDocumentor --trust-gpg-keys 8AC0BAA79732DD42
    
          - name: Cache phpDocumentor build files
            id: phpdocumentor-cache
            uses: actions/cache@v4
            with:
              path: phpdoc-cache
              key: ${{ runner.os }}-phpdocumentor-${{ github.sha }}
              restore-keys: ${{ runner.os }}-phpdocumentor-
    
          # Notice: -d xdebug.mode=off is required due to a bug/workaround
          # Issue: https://github.com/phpDocumentor/phpDocumentor/issues/3642#issuecomment-1912354577
          - name: Build with phpDocumentor
            run: php -d xdebug.mode=off ${{ github.workspace }}/tools/phpDocumentor run -vv -d source --target docs --cache-folder phpdoc-cache --template default
    
          - name: Upload artifact to GitHub Pages
            uses: actions/upload-pages-artifact@v3
            with:
              path: docs
    
      deploy:
        needs: build
    
        # Grant GITHUB_TOKEN the permissions required to make a Pages deployment
        permissions:
          pages: write      # to deploy to Pages
          id-token: write   # to verify the deployment originates from an appropriate source
        
        # Deploy to the github-pages environment
        environment:
          name: github-pages
          url: ${{ steps.deployment.outputs.page_url }}
          
        runs-on: ubuntu-latest
        steps:
          - name: Deploy to GitHub Pages
            id: deployment
            uses: actions/deploy-pages@v4

GitLab-ci
=========

phpDocumentor is shipped as a Docker image which makes it easy to use in a GitLab-ci pipeline. The following
example shows how to use phpDocumentor in a GitLab-ci pipeline and publish it to `GitLab pages`_.

.. hint::

   Gitlab-ci is always executing ``sh`` in a docker container. Because our image has an entrypoint, you need to
   override it with an empty array to make this work.

.. code-block:: yaml

    pages:
      image:
        name: phpdoc/phpdoc
        entrypoint: [""]
      script:
        - phpdoc run -t public
      artifacts:
        paths:
          - public
      only:
        - main

.. _GitLab pages: https://docs.gitlab.com/ee/user/project/pages/
