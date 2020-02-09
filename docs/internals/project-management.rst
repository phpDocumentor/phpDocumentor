Project Management
==================

.. note:: This documentation is about how the phpDocumentor team is organized and how it determines priorities and which
          tooling to use. When you are a user of phpDocumentor you can safely ignore this piece of documentation.

.. note:: As with many open source projects, the phpDocumentor team consists of a very small number of core contributors
          who do this in their spare time. This may mean that even if an issue is urgent or able to be quickly responded
          to; it could still take some time because of availability.

Release schedule
----------------

While writing this document, the team has no set release schedule as it is focusing on getting phpDocumentor 3 released.
This version includes significant architectural changes; meaning that it is highly unpredictable when it can be
released. As soon as phpDocumentor 3 is released, the team can review and change how they can setup a release schedule.

Workflows
---------

To manage the phpDocumentor project we distinguish between three different workflows:

1. Responding to issues
2. Pull requests
3. Planning and developing of new features

Responding to issues
~~~~~~~~~~~~~~~~~~~~

The phpDocumentor team reviews any incoming issue and performs triage; in this phase the urgency of the issue
is determined and what type of issue this is. We distinguish between bugs, enhancements, research and documentation
issues. During triage an issue is labelled with one of these labels to indicate its type.

When the issue is a question, can be dealt with swiftly or is deemed urgent enough a team member will respond to the
issue as soon as we can.

Issues should preferably be assigned to a milestone; the absence of this may indicate an issue is not planned for
inclusion in a known, upcoming, release. A team member may create a new milestone when deemed necessary and assign
issues there when they consider it for resolving at that time.

When an issue is related to a project, or epic, that is in development. The issue may be assigned to that respective
Github project; see `Planning and developing of new features`_ for more information on the use of Github Projects.

Pull requests
~~~~~~~~~~~~~

When a pull request comes in, this is considered an urgent activity that should be responded to as quickly as
availability allows.

Please refer to the CONTRIBUTING.md in the root of this project for what we expect from a pull request. When a pull
request does not match this criteria it is vital to be as helpful as can be and guide the issuer on how to help us get
the pull request included.

There are times when the content of a pull request does not match the strategy or intended architecture of
phpDocumentor. In these cases it may happen that the team is not able to accept a contribution. This does not mean a
pull request is bad or we do not appreciate the time that the issuer has spent; sometimes things do not fit into
the whole or have an, unforeseeable, adverse impact elsewhere.

Planning and developing of new features
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For larger efforts, those that do not fit into one issue, we make a `Github Project on the organisational level`_.
This project represents an epic or otherwise significant effort that we need to keep track of. This does not include
the release of new versions of phpDocumentor as these are tracked using milestones.

.. note: The intended difference in use between milestones and projects is that a project does not need to be completed
         withing the span of one release; by distinguishing between milestones/releases and projects/epics we are able
         to do so.

Since a larger effort may span one or more repositories that we consume, a project on the organisational level is
preferred. Projects on a repository level are discouraged and should either be closed as soon as possible or migrated
to a organisational level.

This is also done so that there is always a clear list of priorities and that the team is able to view all on-going
projects/epics.

As a preference, projects should be made public so that the community can also benefit from this and view what the team
is working on. Private projects are allowed for various reasons, for example: unannounced experimental features or
proof of concepts.

.. _Github Project on the organisational level: https://github.com/orgs/phpDocumentor/projects
