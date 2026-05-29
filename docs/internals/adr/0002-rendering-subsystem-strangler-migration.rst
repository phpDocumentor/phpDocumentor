2. Introduce Rendering subsystem with Action bus and compatibility context
==========================================================================

Date: 2026-05-29
Status: Proposal

Context
-------

The current rendering flow is centered around the legacy Transformer subsystem,
including the transform pipeline stage and writer-based orchestration.

This works, but it tightly couples templates to Descriptor internals and makes
it difficult to evolve the internal model without causing breaking changes for
existing templates.

We want to introduce a new rendering design that:

- uses clearer architectural boundaries,
- supports a stable template-facing model,
- allows incremental migration with low risk,
- preserves compatibility for existing templates during transition.

Decision
--------

We introduce a new Rendering subsystem in parallel to the existing Transformer,
using a strangler migration pattern.

The new subsystem adopts the concept of Actions dispatched through a
synchronous Render CommandBus.

Rendering runtime data is represented through a RenderContext. To keep
compatibility with existing templates, a dedicated compatibility model provides
the global variables expected by legacy templates.

Architecture
------------

Bounded context
~~~~~~~~~~~~~~~

A new ``phpDocumentor\Rendering`` bounded context is introduced, separate from
the legacy ``phpDocumentor\Transformer`` namespace.

Application layer
~~~~~~~~~~~~~~~~~

The application layer contains:

- immutable Render Actions,
- a synchronous Render CommandBus,
- one handler per action,
- middleware for cross-cutting concerns (timing, logging, events).

Handlers orchestrate rendering services and do not contain low-level rendering
logic.

Rendering core
~~~~~~~~~~~~~~

The rendering core contains ports and services such as:

- RenderEngine (Twig-backed initially),
- ArtifactWriter,
- template resolution,
- artifact path resolution,
- ViewModel mapping.

Compatibility layer
~~~~~~~~~~~~~~~~~~~

Compatibility globals are centralized in one component (compatibility context
provider) and injected into the RenderContext. This prevents ad-hoc global
state mutations and keeps legacy behavior explicit and testable.

Migration strategy
------------------

The migration is phased and reversible:

1. Introduce rendering contracts, actions, command bus, and context in parallel
   with no behavior change by default.
2. Implement the new renderer using existing Twig infrastructure where useful.
3. Add a new rendering stage in parallel to the current transform stage,
   controlled by a configuration feature flag.
4. Keep existing Transformer-based rendering as fallback during transition.
5. Flip default to the new renderer only after parity is achieved.
6. Remove legacy Transformer rendering in a later major release.

Backward compatibility policy
-----------------------------

During migration:

- existing templates remain supported,
- legacy template globals are provided through the compatibility context,
- lifecycle compatibility is preserved through event aliasing where needed,
- deprecations are communicated before removal.

The long-term target is a stable template-facing model that does not require
direct dependency on Descriptor internals.

Feature flag
------------

The runtime switch between legacy Transformer and new Renderer is controlled by
an explicit setting in the user configuration.

Proposed initial setting:

- ``<setting name="renderer.enabled" value="true"/>``

Behavior:

- missing or ``false`` (default): execute legacy Transformer stage.
- ``true``: execute new Renderer stage.

This enables incremental adoption and safe rollback without code changes.

Alternatives considered
-----------------------

Big-bang replacement of Transformer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Rejected due to high migration risk, weak rollback options, and likely breakage
for templates and extensions.

Continue evolving Transformer directly
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Rejected because it preserves tight coupling and does not establish clean
rendering boundaries.

Immediate hard switch to ViewModel-only templates
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Rejected for initial rollout because it would create unnecessary compatibility
breaks for existing template ecosystems.

Consequences
------------

Positive
~~~~~~~~

- cleaner separation of concerns,
- clearer extension points for rendering,
- improved ability to evolve internal models,
- safer incremental delivery through strangler migration.

Negative
~~~~~~~~

- temporary duplication of rendering paths,
- short-term maintenance cost for compatibility adapters,
- additional parity testing burden during transition.

Risks and mitigations
---------------------

Risk: behavior drift in compatibility globals
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Mitigation: one centralized compatibility provider and output parity tests.

Risk: extension breakage from lifecycle changes
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Mitigation: preserve event compatibility during migration and document
replacement hooks.

Risk: architecture complexity
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Mitigation: keep command bus synchronous, limit middleware, and defer optional
concerns until parity is stable.

Acceptance criteria
-------------------

The decision is considered successful when:

- the new renderer can run in parallel with the legacy transformer,
- a configuration feature flag can switch between legacy Transformer and new Renderer,
- compatibility globals are provided consistently through RenderContext,
- output parity is demonstrated for core templates in compatibility mode,
- a deprecation path for legacy transformer rendering is documented.
