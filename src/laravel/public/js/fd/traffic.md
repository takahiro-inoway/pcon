# traffic.js

## flow
``` mermaid
graph TB
  A("Browser") --submit---> Routing 
  Viewist ---> A("Browser")
  Routing --> Controllist
  Controllist --> Viewist
  Controllist --> Modelist
  Controllist --> Module
  Modelist --> Controllist
  Module --> Controllist
```

## Routing
``` mermaid
graph LR
  A("Browser") --get---> Routing 
  A("Browser") --post---> Routing 
  Routing --> Controllist:A
  Routing --> Controllist:B
  Routing --> Controllist:C
```

## Viewing
``` mermaid
graph LR  
  Viewist --Html---> A("Browser")
  Viewist --event---> A("Browser")
```
