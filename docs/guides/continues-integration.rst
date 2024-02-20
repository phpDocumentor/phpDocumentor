=====================
Continues Integration
=====================

Just like any other application component your documentation can be build automatically using a continues
integration service. This is a great way to ensure that your documentation is always up to date and that
it is build correctly. This chapter will show you how to use phpDocumentor in combination with different
continues integration services.

GitHub Actions
==============

<to be written>

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
