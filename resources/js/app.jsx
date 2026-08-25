import 'dhtmlx-gantt/codebase/dhtmlxgantt.css';
import '../css/app.css';
import './bootstrap';

import React from 'react';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';

class GlobalErrorBoundary extends React.Component {
  constructor(props) {
    super(props);
    this.state = { hasError: false, error: null, errorInfo: null };
  }
  static getDerivedStateFromError(error) {
    return { hasError: true, error };
  }
  componentDidCatch(error, errorInfo) {
    this.setState({ errorInfo });
    console.error("Global React Error:", error, errorInfo);
  }
  render() {
    if (this.state.hasError) {
      return (
        <div style={{ padding: '20px', background: 'red', color: 'white', zIndex: 9999, position: 'relative', height: '100vh', width: '100vw' }}>
          <h1>Global React Crashed!</h1>
          <pre style={{whiteSpace: 'pre-wrap'}}>{this.state.error && this.state.error.toString()}</pre>
          <pre style={{whiteSpace: 'pre-wrap'}}>{this.state.errorInfo && this.state.errorInfo.componentStack}</pre>
        </div>
      );
    }
    return this.props.children;
  }
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx'),
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(<GlobalErrorBoundary><App {...props} /></GlobalErrorBoundary>);
    },
    progress: {
        color: '#4B5563',
    },
});

