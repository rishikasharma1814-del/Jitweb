import http.server
import socketserver
import os

PORT = 3000

REWRITES = {
    '/product/tattoo': '/tattoo.html',
    '/product/tattoo/': '/tattoo.html',
    '/tattoo': '/tattoo.html',
    '/product': '/business-coaching.html',
    '/product/': '/business-coaching.html',
    '/business-coaching': '/business-coaching.html',
    '/contact': '/contact.html',
}

class RewriteHandler(http.server.SimpleHTTPRequestHandler):
    def do_GET(self):
        raw_path = self.path.split('?')[0]
        
        # If user visits /tattoo.html directly, redirect cleanly to /product/tattoo
        if raw_path == '/tattoo.html':
            self.send_response(302)
            self.send_header('Location', '/product/tattoo')
            self.end_headers()
            return

        if raw_path in REWRITES:
            self.path = REWRITES[raw_path]
        elif raw_path.startswith('/product/assets/'):
            self.path = raw_path.replace('/product/assets/', '/assets/')
        elif raw_path.startswith('/product/') and not raw_path.startswith('/product/tattoo'):
            remainder = raw_path[len('/product/'):]
            target = '/' + remainder
            if not target.endswith('.html') and '.' not in target:
                target += '.html'
            self.send_response(302)
            self.send_header('Location', target)
            self.end_headers()
            return

        return super().do_GET()

if __name__ == '__main__':
    os.chdir(os.path.dirname(os.path.abspath(__file__)))
    socketserver.TCPServer.allow_reuse_address = True
    with socketserver.TCPServer(("", PORT), RewriteHandler) as httpd:
        print(f"==================================================")
        print(f" JIT Website Local Server Running!")
        print(f" Open in Browser: http://localhost:{PORT}")
        print(f" Tattoo Studio:   http://localhost:{PORT}/product/tattoo")
        print(f" Coaching:        http://localhost:{PORT}/business-coaching.html")
        print(f" Press Ctrl+C to stop the server")
        print(f"==================================================")
        try:
            httpd.serve_forever()
        except KeyboardInterrupt:
            print("\nServer stopped.")
