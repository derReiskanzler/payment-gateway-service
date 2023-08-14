# Apply snake_case to Event Payload

This guide represents the way to ensure `snake_case` applied for Event Payload as described in [Allmyhomes guidelines][1]

## How it works

The snake_case is by default applied using Event Payload translator when generating a `Generic` event to `Domain`
event and other way around.

- For that, we inject `SnakeCaseEventPayloadTranslator` in `EventTranslator`. This part is handled automatically for teams that will build their events using ES/CQRS boilerplate.

- To ensure backward compatibility for services using ES/CQRS which was previously using camelCase,we provide a backward compatible class `EventPayloadTranslator` which could be used in `ProophServiceProvider` -> `bindEventPayloadTranslator()` by replacing `SnakeCaseEventPayloadTranslator` to `EventPayloadTranslator` until the service plan a migration process to use snake_case.

[1]: <https://gitlab.smartexpose.com/allmyhomes/technology/-/blob/master/backend/events-guidelines/Events.md>
