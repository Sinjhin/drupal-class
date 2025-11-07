<template>
  <div class="container">
    <h1>TODO App</h1>

    <div class="info-card">
      <p>
        <strong>Drupal CMS Admin:</strong>
        <a href="http://web.4rd.ai:3000" target="_blank" rel="noopener noreferrer">http://web.4rd.ai:3000</a>
      </p>
      <p>Default credentials: <code>admin</code> / <code>admin</code></p>
      <p>
        <strong>Source Code:</strong>
        <a href="https://github.com/Sinjhin/drupal-class" target="_blank" rel="noopener noreferrer">https://github.com/Sinjhin/drupal-class</a>
      </p>
    </div>

    <div v-if="error" class="error">
      <strong>Error:</strong> {{ error }}
    </div>

    <div class="todo-form">
      <div class="todo-input-group">
        <input
          v-model="newTodoTitle"
          @keyup.enter="addTodo"
          type="text"
          placeholder="What needs to be done?"
          class="todo-input"
        />
        <button @click="addTodo" class="btn btn-primary">Add TODO</button>
      </div>
    </div>

    <div v-if="loading" class="loading">Loading TODOs...</div>

    <div v-else-if="todos.length === 0" class="empty-state">
      <p>No TODOs yet!</p>
      <p>Add one above to get started.</p>
    </div>

    <div v-else class="todo-list">
      <div
        v-for="todo in todos"
        :key="todo.id"
        :class="['todo-item', { completed: todo.completed }]"
      >
        <input
          type="checkbox"
          :checked="todo.completed"
          @change="toggleTodo(todo)"
          class="todo-checkbox"
        />
        <span class="todo-text">{{ todo.title }}</span>
        <button @click="deleteTodo(todo)" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</template>

<script setup>
const todos = ref([])
const newTodoTitle = ref('')
const loading = ref(false)
const error = ref(null)

// Fetch TODOs via Nuxt API proxy
const fetchTodos = async () => {
  loading.value = true
  error.value = null

  try {
    const data = await $fetch('/api/todos')

    todos.value = data.data.map(item => ({
      id: item.id,
      uuid: item.id,
      nid: item.attributes.drupal_internal__nid,
      title: item.attributes.title,
      completed: item.attributes.field_completed || false,
    }))
  } catch (e) {
    error.value = e.message || 'Failed to fetch TODOs'
    console.error('Error fetching TODOs:', e)
  } finally {
    loading.value = false
  }
}

// Add a new TODO
const addTodo = async () => {
  if (!newTodoTitle.value.trim()) return

  error.value = null

  try {
    await $fetch('/api/todos', {
      method: 'POST',
      body: {
        data: {
          type: 'node--todo',
          attributes: {
            title: newTodoTitle.value,
            field_completed: false,
          },
        },
      },
    })

    newTodoTitle.value = ''
    await fetchTodos()
  } catch (e) {
    error.value = e.message || 'Failed to create TODO'
    console.error('Error adding TODO:', e)
  }
}

// Toggle TODO completion status
const toggleTodo = async (todo) => {
  error.value = null

  try {
    await $fetch('/api/todos', {
      method: 'PATCH',
      query: { id: todo.uuid },
      body: {
        data: {
          type: 'node--todo',
          id: todo.uuid,
          attributes: {
            field_completed: !todo.completed,
          },
        },
      },
    })

    await fetchTodos()
  } catch (e) {
    error.value = e.message || 'Failed to update TODO'
    console.error('Error toggling TODO:', e)
  }
}

// Delete a TODO
const deleteTodo = async (todo) => {
  error.value = null

  try {
    await $fetch('/api/todos', {
      method: 'DELETE',
      query: { id: todo.uuid },
    })

    await fetchTodos()
  } catch (e) {
    error.value = e.message || 'Failed to delete TODO'
    console.error('Error deleting TODO:', e)
  }
}

// Fetch TODOs on mount
onMounted(() => {
  fetchTodos()
})
</script>

<style>
/* Dark Mode Theme */
:root {
  --bg-primary: #1a1a1a;
  --bg-secondary: #2d2d2d;
  --bg-hover: #3a3a3a;
  --text-primary: #e0e0e0;
  --text-secondary: #a0a0a0;
  --border-color: #404040;
  --accent-color: #4a9eff;
  --accent-hover: #6bb0ff;
  --success-color: #4caf50;
  --danger-color: #f44336;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  background-color: var(--bg-primary);
  color: var(--text-primary);
  line-height: 1.6;
}

.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
}

h1 {
  font-size: 2.5rem;
  margin-bottom: 2rem;
  color: var(--text-primary);
  text-align: center;
}

.todo-form {
  background-color: var(--bg-secondary);
  padding: 1.5rem;
  border-radius: 8px;
  margin-bottom: 2rem;
  border: 1px solid var(--border-color);
}

.todo-input-group {
  display: flex;
  gap: 1rem;
}

.todo-input {
  flex: 1;
  padding: 0.75rem;
  background-color: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: 4px;
  color: var(--text-primary);
  font-size: 1rem;
}

.todo-input:focus {
  outline: none;
  border-color: var(--accent-color);
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s;
  font-weight: 500;
}

.btn-primary {
  background-color: var(--accent-color);
  color: white;
}

.btn-primary:hover {
  background-color: var(--accent-hover);
}

.btn-danger {
  background-color: var(--danger-color);
  color: white;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

.btn-danger:hover {
  background-color: #d32f2f;
}

.todo-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.todo-item {
  background-color: var(--bg-secondary);
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: background-color 0.2s;
}

.todo-item:hover {
  background-color: var(--bg-hover);
}

.todo-item.completed .todo-text {
  text-decoration: line-through;
  color: var(--text-secondary);
}

.todo-checkbox {
  width: 20px;
  height: 20px;
  cursor: pointer;
  accent-color: var(--success-color);
}

.todo-text {
  flex: 1;
  font-size: 1rem;
  color: var(--text-primary);
}

.loading {
  text-align: center;
  padding: 2rem;
  color: var(--text-secondary);
  font-size: 1.1rem;
}

.error {
  background-color: rgba(244, 67, 54, 0.1);
  border: 1px solid var(--danger-color);
  color: var(--danger-color);
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 2rem;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: var(--text-secondary);
}

.empty-state p {
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}

.info-card {
  background-color: var(--bg-secondary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 2rem;
}

.info-card p {
  margin-bottom: 0.75rem;
  line-height: 1.6;
}

.info-card p:last-child {
  margin-bottom: 0;
}

.info-card a {
  color: var(--accent-color);
  text-decoration: none;
  transition: color 0.2s;
}

.info-card a:hover {
  color: var(--accent-hover);
  text-decoration: underline;
}

.info-card code {
  background-color: var(--bg-primary);
  padding: 0.2rem 0.4rem;
  border-radius: 3px;
  font-family: 'Courier New', monospace;
  font-size: 0.9rem;
  color: var(--accent-color);
}
</style>
